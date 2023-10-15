<?php

namespace Leinc\MinichanRouter\Router\Interfaces;

use Leinc\MinichanRouter\Router\Request;

interface IFormRequest {
    public function validate(Request $request);
}