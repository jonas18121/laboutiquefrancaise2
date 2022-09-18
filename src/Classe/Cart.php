<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

Class Cart 
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Add a product into cart
     *
     * @param int $id
     * @return void
     */
    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        // Si le panier contient un produit qui à un id déjà présent dans le panier,
        // on fait ++ pour faire +1 à la quantité, on passe de 1 à 2 par exemple
        // si c'est la première fois que le produit est ajouter au panier, on ajoute 1
        if (!empty($cart[$id])) {
            $cart[$id]++;
        }
        else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    /**
     * Display products into cart
     *
     * @return array
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    /**
     * Remove all products into cart
     *
     * @return void
     */
    public function remove()
    {
        return $this->session->remove('cart');
    }
}