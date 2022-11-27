<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

Class Cart 
{
    private $session;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
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

    /**
     * Delete one product into cart
     *
     * @return array
     */
    public function delete(int $id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    /**
     * Delete one quantity of product or remove into cart
     *
     * @return array
     */
    public function decrease(int $id)
    {
        $cart = $this->session->get('cart', []);

        // Si le panier contient un produit qui à un id déjà présent dans le panier,
        // on fait -- pour faire -1 à la quantité, on passe de 2 à 1 par exemple
        // si la quantité est égale à 1, on supprime le produit
        if ($cart[$id] > 1) {
            $cart[$id]--;
        }
        else {
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }

    /**
     * Display products into cart with info complete
     *
     * @return array
     */
    public function getFull()
    {
        $cartComplete = [];

        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->em->getRepository(Product::class)->findOneById($id);

                // Delete $id in cart if product don't exist into bdd
                if (!$product_object) {
                    $this->delete($id);

                    continue; // continue the loop
                }
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }
}