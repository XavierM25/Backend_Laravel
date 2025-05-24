<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Restablecer la contraseña UCV Deportes</title>
  <style type="text/css">
    body {
      width: 100% !important;
      height: 100%;
      margin: 0;
      line-height: 1.4;
      background-color: #F2F4F6;
      color: #74787E;
      -webkit-text-size-adjust: none;
    }
    @media only screen and (max-width: 600px) {
      .email-body_inner {
        width: 100% !important;
      }
      .email-footer {
        width: 100% !important;
      }
    }
    @media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
      }
    }
  </style>
</head>
<body style="background-color: #F2F4F6; color: #74787E; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; height: 100%; line-height: 1.4; margin: 0; width: 100% !important;">
  <span class="preheader" style="display: none !important;">Use este enlace para restablecer su contraseña. El enlace solo es válido por 24 horas.</span>
  <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0;">
    <tr>
      <td align="center" style="word-break: break-word;">
        <table class="email-content" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0;">
          <tr>
            <td class="email-masthead" align="center" style="padding: 25px 0; word-break: break-word;">
              <a href="#" class="email-masthead_name" style="color: #bbbfc3; font-size: 16px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                UCV Deportes
              </a>
            </td>
          </tr>
          <tr>
            <td class="email-body" width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; word-break: break-word;" bgcolor="#FFFFFF">
              <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding: 0; width: 570px;" bgcolor="#FFFFFF">
                <tr>
                  <td class="content-cell" style="padding: 35px; word-break: break-word;">
                    <h1 style="color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0;" align="left">Hola, {{ $nombreUsuario }}</h1>
                    <p style="color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0;" align="left">Recientemente solicitó restablecer su contraseña para su cuenta en UCV Deportes. Aquí está su código de verificación:</p>
                    <p style="color: #74787E; font-size: 24px; font-weight: bold; line-height: 1.5em; margin-top: 0; text-align: center;">{{ $otp }}</p>
                    <p style="color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0;" align="left"><strong>Este código solo es válido durante las próximas 24 horas.</strong></p>
                    <p style="color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0;" align="left">Por razones de seguridad, esta solicitud se recibió desde su dispositivo. Si no solicitó restablecer la contraseña, ignore este correo electrónico o póngase en contacto con el servicio de asistencia si tiene alguna pregunta.</p>
                    <p style="color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0;" align="left">Gracias, <br> Equipo de UCV Deportes</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="word-break: break-word;">
              <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding: 0; text-align: center; width: 570px;">
                <tr>
                  <td class="content-cell" align="center" style="padding: 35px; word-break: break-word;">
                    <p class="sub align-center" style="color: #AEAEAE; font-size: 12px; line-height: 1.5em; margin-top: 0;" align="center">© {{ date('Y') }} UCV Deportes. Todos los derechos reservados.</p>
                    <p class="sub align-center" style="color: #AEAEAE; font-size: 12px; line-height: 1.5em; margin-top: 0;" align="center">
                      <br/>Av. Alfredo Mendiola 6232
                      <br />Tel: (01) 202-4342
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
