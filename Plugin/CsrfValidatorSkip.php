<?php

namespace FlexShopper\Payments\Plugin;

class CsrfValidatorSkip {
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        $request,
        $action
    ) {
        if ($request->getModuleName() == 'flexshopper') {
            return; // Skip CSRF check
        }
        $proceed($request, $action); 
    }
}