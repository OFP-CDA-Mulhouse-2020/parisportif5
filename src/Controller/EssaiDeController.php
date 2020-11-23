<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class EssaiDeController{

    
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Nombre aléatoire : '.$number.'</body></html>'
        );
    }

}