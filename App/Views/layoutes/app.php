<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}{% endblock %}</title>
</head>
<body>

  <ul>
    <li><a href="/php-framework/public/Homecontroller/index">Home</a></li>
    <li><a href="/php-framework/public/postsController/index">Posts</a></li>
  </ul>
    
    {% block body %}
    {% endblock %}

</body>
</html>
