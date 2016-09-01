define({ "api": [
  {
    "type": "post",
    "url": "/login",
    "title": "Login",
    "description": "<p>Get API key, client secret and UUID by username and password</p>",
    "name": "PostLogin",
    "group": "Basic",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>User's username.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>User's password.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "main_info",
            "description": "<p>Main user's info.</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "bank_info",
            "description": "<p>Bank info.</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "shipping_info",
            "description": "<p>Shipping info.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "type": "Boolean",
            "optional": false,
            "field": "error",
            "description": "<p>Error status</p>"
          },
          {
            "group": "Error 4xx",
            "type": "String",
            "optional": false,
            "field": "error_message",
            "description": "<p>Description of the error</p>"
          },
          {
            "group": "Error 4xx",
            "type": "Number",
            "optional": false,
            "field": "error_code",
            "description": "<p>Identifier of the error</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "mlm_rest_api/v1/index.php",
    "groupTitle": "Basic"
  },
  {
    "type": "post",
    "url": "/register",
    "title": "Register",
    "description": "<p>Register in the app.</p>",
    "name": "PostRegister",
    "group": "Basic",
    "parameter": {
      "fields": {
        "Main info": [
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name.</p>"
          },
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "surname",
            "description": "<p>Surname.</p>"
          },
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email address.</p>"
          },
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>Phone number in international format.</p>"
          },
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>Username.</p>"
          },
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Password.</p>"
          },
          {
            "group": "Main info",
            "type": "String",
            "optional": false,
            "field": "refer",
            "description": "<p>Username of the referrer.</p>"
          }
        ],
        "Shipping info": [
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_name",
            "description": "<p>Name.</p>"
          },
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_surname",
            "description": "<p>Surname.</p>"
          },
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_address",
            "description": "<p>Full address.</p>"
          },
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_city",
            "description": "<p>City.</p>"
          },
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_postal_code",
            "description": "<p>Postal code.</p>"
          },
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_country",
            "description": "<p>Country.</p>"
          },
          {
            "group": "Shipping info",
            "type": "String",
            "optional": false,
            "field": "shipping_phone",
            "description": "<p>Phone number in international format.</p>"
          }
        ],
        "Bank info": [
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_name",
            "description": "<p>Name.</p>"
          },
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_surname",
            "description": "<p>Surname.</p>"
          },
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_iban",
            "description": "<p>IBAN.</p>"
          },
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_swift_code",
            "description": "<p>Swift code.</p>"
          },
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_paypal",
            "description": "<p>Paypal email.</p>"
          },
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_debit_card",
            "description": "<p>Number of debit card.</p>"
          },
          {
            "group": "Bank info",
            "type": "String",
            "optional": false,
            "field": "bank_personal_code",
            "description": "<p>Personal code.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "main_info",
            "description": "<p>Main user's info.</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "bank_info",
            "description": "<p>Bank info.</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "shipping_info",
            "description": "<p>Shipping info.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "type": "Boolean",
            "optional": false,
            "field": "error",
            "description": "<p>Error status</p>"
          },
          {
            "group": "Error 4xx",
            "type": "String",
            "optional": false,
            "field": "error_message",
            "description": "<p>Description of the error</p>"
          },
          {
            "group": "Error 4xx",
            "type": "Number",
            "optional": false,
            "field": "error_code",
            "description": "<p>Identifier of the error</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "mlm_rest_api/v1/index.php",
    "groupTitle": "Basic"
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
  },
  {
    "type": "post",
    "url": "/restore/code",
    "title": "Request code",
    "description": "<p>Request code generation.</p>",
    "name": "PostRequestCodeGen",
    "group": "_Password_restore_",
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
            "type": "Boolean",
            "optional": false,
            "field": "error",
            "description": "<p>Error status</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "error_message",
            "description": "<p>Description of the error</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "error_code",
            "description": "<p>Identifier of the error</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "type": "Boolean",
            "optional": false,
            "field": "error",
            "description": "<p>Error status</p>"
          },
          {
            "group": "Error 4xx",
            "type": "String",
            "optional": false,
            "field": "error_message",
            "description": "<p>Description of the error</p>"
          },
          {
            "group": "Error 4xx",
            "type": "Number",
            "optional": false,
            "field": "error_code",
            "description": "<p>Identifier of the error</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "mlm_rest_api/v1/index.php",
    "groupTitle": "_Password_restore_"
  }
] });
