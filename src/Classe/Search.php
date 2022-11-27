<?php

namespace App\Classe;

use App\Entity\Category;

Class Search 
{   
    /**
     * Name's product that user search
     * 
     * @var string|null
     */
    private $string = '';

    /**
     * List categories
     * 
     * The user select one category
     * 
     * @var Category[]|null
     */
    private  $categories = [];

    /**
     * Get name's product that user search
     *
     * @return  string|null
     */ 
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set name's product that user search
     *
     * @param  string|null  $string  Name's product that user search
     *
     * @return  self
     */ 
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * Get the user select one category
     *
     * @return  Category[]|null
     */ 
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set the user select one category
     *
     * @param  Category[]|null  $categories  The user select one category
     *
     * @return  self
     */ 
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }
}