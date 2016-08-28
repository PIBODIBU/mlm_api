define({ "api": [
  {
    "type": "post",
    "url": "/password/restore",
    "title": "Get all users",
    "name": "PostRestorePasswordWithEmail",
    "group": "Security",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>User's email for password restoring.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>Restore code.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "mlm_rest_api/v1/index.php",
    "groupTitle": "Security"
  },
  {
    "type": "get",
    "url": "/users",
    "title": "Get all users",
    "name": "GetUsers",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "api_key",
            "description": "<p>User's API key.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "size": "0-30",
            "optional": false,
            "field": "limit",
            "description": "<p>Result limit.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "offset",
            "description": "<p>Result offset.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "signature",
            "description": "<p>Signature of the request.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object[]",
            "optional": false,
            "field": "users",
            "description": "<p>List of user profiles.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "mlm_rest_api/v1/index.php",
    "groupTitle": "User"
  }
] });
