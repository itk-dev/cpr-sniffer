<?php

$finder = PhpCsFixer\Finder::create()
  ->in(__DIR__.'/{src,tests}')
;

return (new PhpCsFixer\Config())
  ->setRules([
      '@PSR2' => true,
      '@Symfony' => true,
      '@Symfony:risky' => false,
      'phpdoc_align' => false,
      'no_superfluous_phpdoc_tags' => false,
      'array_syntax' => ['syntax' => 'short'],
  ])
  ->setFinder($finder);
