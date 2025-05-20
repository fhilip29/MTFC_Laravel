<?php

namespace App\Services;

class ContentScreeningService
{
    private $badWords = [
        // Add your list of bad words here
        'fuck', 'Fuck', 'FUCK','nigga', 'Nigga', 'NIGGA', 'Putangina', 'putangina', 'retard', 'Retard', 'RETARD', 'tangina', 'TANGINA', 'Tangina','pok-pok', 'POKPOK', 'POK-POK',
        'porn', 'Porn', 'PORN', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub.com', 'Pornhub.com', 'PORNHUB.COM', 'pornhub.com', 'Pornhub.com', 'PORNHUB.COM', 'pornhub.com', 'Pornhub.com', 'PORNHUB.COM',
        'sex', 'Sex', 'SEX', 'sexy', 'Sexy', 'SEXY', 'sexy', 'Sexy', 'SEXY', 'sexy', 'Sexy', 'SEXY', 'sexy', 'Sexy', 'SEXY', 'sexy', 'Sexy', 'SEXY', 'sexy', 'Sexy', 'SEXY', 'sexy', 'Sexy', 'SEXY',
        'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH',
        'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT',
        'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA', 'nigga', 'Nigga', 'NIGGA',
        'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB', 'pornhub', 'Pornhub', 'PORNHUB',
        'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL', 'inutil', 'Inutil', 'INUTIL',
        'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA', 'putangina', 'Putangina', 'PUTANGINA',
        'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK', 'fuck', 'Fuck', 'FUCK',
        'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD', 'retard', 'Retard', 'RETARD',
        'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO', 'bobo', 'Bobo', 'BOBO',
        'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO', 'tarantado', 'Tarantado', 'TARANTADO',
        'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT', 'cunt', 'Cunt', 'CUNT',
        'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE', 'tae', 'Tae', 'TAE',
        'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE', 'tite', 'Tite', 'TITE',
        'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA', 'punyeta', 'Punyeta', 'PUNYETA',
        'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO', 'gago', 'Gago', 'GAGO',
        'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI', 'gagi', 'Gagi', 'GAGI',
        'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA', 'tanga', 'Tanga', 'TANGA',
        'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA', 'pukingina', 'Pukingina', 'PUKINGINA',
        'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO', 'siraulo', 'Siraulo', 'SIRAULO',
        'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG', 'malibog', 'Malibog', 'MALIBOG',
        'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY', 'mamatay', 'Mamatay', 'MAMATAY',
        'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG', 'libog', 'Libog', 'LIBOG',
        'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE', 'asshole', 'Asshole', 'ASSHOLE',
        'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH', 'bitch', 'Bitch', 'BITCH',
        'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT', 'hindot', 'Hindot', 'HINDOT',
        'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER', 'motherfucker', 'Motherfucker', 'MOTHERFUCKER',
        'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER', 'cocksucker', 'Cocksucker', 'COCKSUCKER',
        'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT', 'shit', 'Shit', 'SHIT',
        'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP', 'sarap', 'Sarap', 'SARAP',
        'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE', 'babe', 'Babe', 'BABE',
        'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY', 'baby', 'Baby', 'BABY',
        'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX', 'flix', 'Flix', 'FLIX',
        'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK', 'sulasok', 'Sulasok', 'SULASOK',
        'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN', 'kantutan', 'Kantutan', 'KANTUTAN',
        'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT', 'kantot', 'Kantot', 'KANTOT',
        'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN', 'tirahin', 'Tirahin', 'TIRAHIN',
        'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET', 'pwet', 'Pwet', 'PWET',
        'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI', 'puki', 'Puki', 'PUKI',
        'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS', 'ass', 'Ass', 'ASS',
        'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK', 'dick', 'Dick', 'DICK',
        'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY', 'pussy', 'Pussy', 'PUSSY',
        'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT', 'faggot', 'Faggot', 'FAGGOT',
        'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN', 'kantotan', 'Kantotan', 'KANTOTAN',
    ];

    public function screenContent(string $content): array
    {
        $issues = [];
        
        // Check for bad words
        foreach ($this->badWords as $word) {
            if (stripos($content, $word) !== false) {
                $issues[] = "Content contains inappropriate language.";
                break;
            }
        }

        // Check for unprotected links
        if (preg_match('/https?:\/\/(?!www\.|https?:\/\/www\.)[^\s]+/', $content, $matches)) {
            $issues[] = "Content contains unprotected links. Please use https:// links only.";
        }

        return $issues;
    }

    public function isContentSafe(string $content): bool
    {
        return empty($this->screenContent($content));
    }
} 