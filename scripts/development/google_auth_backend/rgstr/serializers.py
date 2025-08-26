from rest_framework import serializers
from django.contrib.auth.models import User
from django.contrib.auth import get_user_model
from allauth.socialaccount.models import SocialAccount
import os
 
class JWTSerializer(serializers.Serializer):
    access = serializers.CharField()
    refresh = serializers.CharField()

class SocialAccountSerializer(serializers.ModelSerializer):
    class Meta:
        model = SocialAccount
        fields = ('provider', 'uid', 'extra_data')

User = get_user_model()

class UserSerializer(serializers.ModelSerializer):
    social_accounts = serializers.SerializerMethodField()
    image = serializers.SerializerMethodField()
    bio = serializers.SerializerMethodField()

    class Meta:
        model = User
        fields = ['id', 'username', 'email','image', 'bio','social_accounts']
    
    def get_image(self, user):
        main_url = os.getenv("MAIN_URL") 
        return main_url + user.profile.avatar.url
    
    def get_bio(self, user):
        return user.profile.bio
    
    def get_social_accounts(self, obj):
        accounts = SocialAccount.objects.filter(user=obj)
        return SocialAccountSerializer(accounts, many=True).data