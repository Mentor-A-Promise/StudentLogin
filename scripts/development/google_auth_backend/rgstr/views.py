import logging
import os
import traceback
import requests
from django.contrib.auth import get_user_model
from django.core.files.base import ContentFile
from django.core.files import File
from rest_framework import permissions
from rest_framework.views import APIView
from rest_framework.response import Response
from dj_rest_auth.registration.views import SocialLoginView
from allauth.socialaccount.providers.google.views import GoogleOAuth2Adapter
from allauth.socialaccount.models import SocialAccount, SocialApp
from .models import Profile
from .serializers import UserSerializer
from rest_framework_simplejwt.tokens import RefreshToken
import shortuuid # pyright: ignore[reportMissingImports]
from rest_framework.permissions import IsAuthenticated
from allauth.socialaccount.providers.oauth2.client import OAuth2Error
from allauth.socialaccount.helpers import complete_social_login
from rest_framework_simplejwt.serializers import TokenRefreshSerializer
from rest_framework.decorators import api_view
from rest_framework.exceptions import ValidationError
from dotenv import load_dotenv

load_dotenv()

logger = logging.getLogger(__name__)
User = get_user_model()

#Generate JWT for the user
def get_tokens_for_user(user):
    refresh = RefreshToken.for_user(user)
    return {"refresh": str(refresh), "access": str(refresh.access_token)}

#Create unique username
def create_username(email):
    base = email.split('@')[0][:20]
    for _ in range(5):
        uname = f"{base}_{shortuuid.uuid()}".lower()
        if not User.objects.filter(username=uname).exists():
            return uname
    raise Exception("Failed to create unique username")

#Handles login/signup using Google
class GoogleLogin(SocialLoginView):
    adapter_class = GoogleOAuth2Adapter
    callback_url = os.getenv("GOOGLE_REDIRECT_URL")

#Return authenticated user profile (secured)
class UserMe(APIView):
    permission_classes = (permissions.IsAuthenticated,)

    def get(self, request):
        serializer = UserSerializer(request.user)
        return Response(serializer.data)

#Save avatar and extra Google info when login completes
def save_google_profile(user):
    try:
        social_account = SocialAccount.objects.filter(user=user, provider="google").first()
        if not social_account:
            return

        extra_data = social_account.extra_data
        profile, _ = Profile.objects.get_or_create(user=user)

        #Save avatar from Google
        image_url = extra_data.get("picture")
        if image_url:
            resp = requests.get(image_url, timeout=5)
            if resp.status_code == 200:
                file_name = f"{user.username}.png"
                image_file = ContentFile(resp.content)
                profile.avatar.save(file_name, File(image_file), save=True)

        #Save bio, name, etc.
        if not user.first_name:
            user.first_name = extra_data.get("given_name", "")
        if not user.last_name:
            user.last_name = extra_data.get("family_name", "")
        user.save()
    except Exception as e:
        logger.warning(f"Failed to save Google profile: {e}")

#Account linking
class LinkGoogleAccountView(APIView):
    permission_classes = [IsAuthenticated]

    def post(self, request):
        access_token = request.data.get("access_token")
        if not access_token:
            return Response({"detail": "Access token missing"}, status=400)

        #Simulate Google login to attach the social account
        adapter = GoogleOAuth2Adapter(request)
        app = SocialApp.objects.get(provider="google")
        token = adapter.parse_token({'access_token': access_token})
        token.app = app
        #Fetch user info from Google
        print("Starting Google login")
        try:
            login = adapter.complete_login(request, app, token, response={'access_token': access_token})
            # print("finished  Google login")
            login.token = token
            # login.state = SocialLoginView.unstash_state(request)
            login.user = request.user
        except SocialApp.DoesNotExist:
            return Response({"detail": "Google provider not configured"}, status=500)
        except OAuth2Error as e:
            # print("oauth2 error")
            return Response({"detail": "Google authentication failed"}, status=400)
        
        existing_account = SocialAccount.objects.filter(provider='google', uid=login.account.uid).first()
        if existing_account:
            if existing_account.user == request.user:
                # The account is already linked to this user
                return Response({"detail": "Google account already linked to this user."}, status=200)
            else:
                # The account is linked to another user — deny linking
                return Response({"detail": "This Google account is already linked to another user."}, status=400)

        try:
            complete_social_login(request, login)
        except Exception as e:
            print("exception during login",str(e))
            traceback.print_exc()  
            return Response({"detail": "Could not complete social login"}, status=500)

        return Response({"detail": "Google account linked successfully."}, status=200)
    
@api_view(['POST'])
def refresh_token_view(request):
    serializer = TokenRefreshSerializer(data=request.data)
    try:
        serializer.is_valid(raise_exception=True)
        return Response(serializer.validated_data, status=200)
    except ValidationError as e:
        return Response({"detail": "Invalid or expired refresh token"}, status=401)

