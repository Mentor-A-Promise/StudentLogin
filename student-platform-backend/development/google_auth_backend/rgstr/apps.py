from django.apps import AppConfig


class RgstrConfig(AppConfig):
    default_auto_field = "django.db.models.BigAutoField"
    name = "rgstr"

    def ready(self):
        import rgstr.signals
