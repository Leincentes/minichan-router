<?php

namespace MinichanRouter\Router\Interfaces;

use MinichanRouter\Router\Request;

interface IFormRequest {
    public function validate(Request $request);
}