<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation ColocApp</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; background-color: #f9fafb; margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background: #6366f1; color:#fff; padding:20px; text-align:center;">
                            <h1>Vous êtes invité sur ColocApp!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px; color:#333;">
                            <p>Bonjour,</p>
                            <p><strong>{{ $invitedBy->name }}</strong> vous a invité à rejoindre la colocation <strong>{{ $colocation->nom }}</strong>.</p>
                            <p>Pour accepter ou refuser l'invitation, cliquez sur le lien ci-dessous :</p>
                            <p style="text-align:center; margin:30px 0;"><a href="{{ url('/invitations/respond?token='.$token.'&email='.$email) }}" style="background:#6366f1; color:#fff; padding:10px 20px; border-radius:4px; text-decoration:none;">Voir l'invitation</a></p>
                            <p>Si vous ne souhaitez pas rejoindre, vous pouvez ignorer ce message ou cliquer sur ce lien :</p>
                            <p style="text-align:center; margin:20px 0;"><a href="{{ url('/invitations/decline?token='.$token.'&email='.$email) }}" style="color:#6366f1; text-decoration:underline;">Refuser</a></p>
                            <p>Ce lien expirera dans 7 jours.</p>
                            <p>Merci,<br>L'équipe ColocApp</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f3f4f6; padding:20px; text-align:center; font-size:12px; color:#888;">
                            © 2026 ColocApp - Tous droits réservés
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>