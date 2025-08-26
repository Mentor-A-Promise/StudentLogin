from django.urls import path
from .views import UserMe, GoogleLogin, UserMe, LinkGoogleAccountView, refresh_token_view
from rest_framework_simplejwt.views import (TokenRefreshView)
from rest_framework_simplejwt.views import TokenBlacklistView
from dj_rest_auth.registration.views import SocialAccountListView, SocialLoginView
from allauth.socialaccount.providers.google.views import GoogleOAuth2Adapter
from dj_rest_auth.registration.views import SocialLoginView

class GoogleLogin(SocialLoginView):
    adapter_class = GoogleOAuth2Adapter

urlpatterns = [
    path('google/login/', GoogleLogin.as_view(), name='google_login'),
    path('users/me/', UserMe.as_view(), name='user_detail'),
    path('token/refresh/', refresh_token_view, name='token_refresh'),
    path('token/blacklist/', TokenBlacklistView.as_view(), name='token_blacklist'),
    path('google/link-account/', LinkGoogleAccountView.as_view(), name='google-link-account'),
    # path('google/login/', GoogleAuthView.as_view(), name='google_login'),
    # path('google-init/', Google_Init, name='google-init'),
    # path('google/callback/', Google_Callback, name='google-callback'),


]