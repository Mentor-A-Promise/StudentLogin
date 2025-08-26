from django.shortcuts import render
import os
from allauth.socialaccount.providers.google.views import GoogleOAuth2Adapter
from allauth.socialaccount.providers.oauth2.client import OAuth2Client
from dj_rest_auth.registration.views import SocialLoginView
from rest_framework.views import APIView
from rest_framework import permissions
from .serializers import UserSerializer 
from rest_framework.response import Response
from rest_framework.generics import GenericAPIView
from rest_framework import status
from rest_framework_simplejwt.tokens import RefreshToken
from django.contrib.auth import get_user_model
from django.contrib.auth.hashers import make_password
from django.contrib.auth.base_user import BaseUserManager
import requests
import logging
import shortuuid # pyright: ignore[reportMissingImports]
from rest_framework.decorators import api_view
from google_auth_oauthlib.flow import Flow
from django.conf import settings
from django.shortcuts import redirect
from dotenv import load_dotenv

load_dotenv()

class UserMe(APIView):
    permission_classes = (permissions.IsAuthenticated,)
    
    def get(self, request):
        serializer = UserSerializer(request.user)
        return Response(serializer.data)
    
logger = logging.getLogger(__name__)

User = get_user_model()

def get_tokens_for_user(user):
    refresh = RefreshToken.for_user(user)
    return {"refresh": str(refresh), "access": str(refresh.access_token)}

def create_username(email):
    
    base = email.split('@')[0][:20]
    for _ in range(5):
        uname = f"{base}_{shortuuid.uuid()}".lower()
        if not User.objects.filter(username=uname).exists():
            return uname
    raise Exception("Failed to create unique username")

 
@api_view(["GET"])
def Google_Init(request):
    
    flow = Flow.from_client_config(
    {
        "web": {
            "client_id": os.getenv("CLIENT_ID"),
            "project_id": os.getenv("GOOGLE_PROJECT_ID"),
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_secret": os.getenv("CLIENT_SECRET"),
            "redirect_uris": [os.getenv("GOOGLE_REDIRECT_URL")],
        }
    },
    scopes=["openid", "email", "profile"],
    redirect_uri=os.getenv("GOOGLE_REDIRECT_URL"),
)
    auth_url, state = flow.authorization_url(
        access_type="offline", include_granted_scopes="true", prompt="consent"
    )
    request.session["state"] = state
    return redirect(auth_url)

@api_view(["GET"])
def Google_Callback(request):
    state = request.session.get("state")
    if not state:
        return Response({"detail": "State not found in session"}, status=400)

    
    flow = Flow.from_client_config(
    {
        "web": {
            "client_id": os.getenv("CLIENT_ID"),
            "project_id": os.getenv("GOOGLE_PROJECT_ID"),
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_secret": os.getenv("CLIENT_SECRET"),
            "redirect_uris": [os.getenv("GOOGLE_REDIRECT_URL")],
        }
    },
    scopes=["openid", "email", "profile"],
    redirect_uri=os.getenv("GOOGLE_REDIRECT_URL"),
    state=state 

)   try:
        flow.fetch_token(authorization_response=request.build_absolute_uri())
    except Exception as e:
        logger.error(f"Token fetch failed: {e}")
        return Response({"detail": "Invalid or expired token."}, status=400)

    credentials = flow.credentials
    try:
        resp = requests.get(
            "https://www.googleapis.com/oauth2/v3/userinfo",
            headers={"Authorization": f"Bearer {credentials.token}"}
        )
        resp.raise_for_status()
        user_data = resp.json()
    except requests.RequestException as e:
        logger.error(f"Failed to fetch user info: {e}")
        return Response({"detail": "Failed to fetch user info."}, status=400)


    # Extract user data
    email = user_data["email"].lower()
    google_id = user_data.get("sub")
    email_verified = user_data.get("email_verified", False)
    if not email_verified:
        return Response({"detail": "Email not verified by Google."}, status=403)

    # create user
    user = User.objects.filter(email=email).first()
    if not user:
        user = User.objects.create_user(
            username=create_username(email),
            email=email,
            first_name=user_data.get("given_name", ""),
            last_name=user_data.get("family_name", ""),
        )
    if not user.is_active:
        return Response({"detail": "Account disabled"}, status=403)

    # Generate token
    token_data = get_tokens_for_user(user)

    # Redirect to frontend with token or return it as JSON
    return Response({
        "user": UserSerializer(user).data,
        "token": token_data
    })


