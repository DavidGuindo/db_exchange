<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'maker.generator' shared service.

include_once $this->targetDirs[3].'\\vendor\\symfony\\maker-bundle\\src\\Generator.php';

return $this->privates['maker.generator'] = new \Symfony\Bundle\MakerBundle\Generator(($this->privates['maker.file_manager'] ?? $this->load('getMaker_FileManagerService.php')), 'App');
