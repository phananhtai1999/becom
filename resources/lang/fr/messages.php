<?php

$maximumCredit = config('limitcredit.maximum_credit');
$minimumCredit = config('limitcredit.minimum_credit');

return [

    'success' => 'Succès',
    'unauthorized' => 'Non autorisé',
    'given_data_invalid' => 'Les données fournies étaient invalides',
    'internal_server_error' => 'Erreur interne du serveur',
    'secret_key_invalid' => 'La clé secrète nest pas valide',
    'email_already_verified' => 'Le-mail a déjà été vérifié',
    'pin_invalid' => 'Le code PIN nest pas valide',
    'login_success' => 'Connectez-vous avec succès',
    'logout_success' => 'Déconnectez-vous avec succès',
    'change_password_success' => 'Changer le mot de passe avec succès',
    'token_does_not_exists' => 'Le jeton nexiste pas',
    'register_success' => 'Inscrivez-vous avec succès',
    'reset_password' => 'Un nouveau mot de passe a été envoyé à votre adresse e-mail!',
    'email_does_not_exists' => 'Le-mail nexiste pas',
    'account_banned' => 'Votre compte est banni du système',
    'account_deleted' => "Votre compte n'est plus disponible dans le système",
    'date_format' => 'La date de naissance ne correspond pas au format A-m-j.',
    'campaign_invalid' => "L'uuid de campagne sélectionné n'est pas valide.",
    'smtp_account_invalid' => "L'uuid de campagne sélectionné n'est pas valide",
    'credit_invalid' => "Le crédit de l'utilisateur n'est pas valide.",
    'send_campaign_success' => 'Envoyer un e-mail en fonction du succès de la campagne.',
    'error_data' => 'erreur en ligne',
    'birthday_campaign_have_not_scenario' => "La campagne d'anniversaire n'est pas la campagne de scénario sélectionnée",
    'minimum_money' => 'Le montant minimum est de 2000 VND',
    'maximum_money' => 'Le montant maximum est de 50000000 VND',
    'limit_maximum_and_minimum_credit' => "Le nombre maximum de crédits est de $maximumCredit, Le nombre minimum de crédits est de $minimumCredit"
];
