<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminLocaleController extends AbstractController
{
    #[Route('/admin/locale/{locale}', name: 'admin_locale', requirements: ['locale' => 'fr|en'])]
    public function __invoke(string $locale, Request $request): RedirectResponse
    {
        if ($request->hasSession()) {
            $request->getSession()->set('_locale', $locale);
        }

        $referer = $request->headers->get('referer');

        if ($referer) {
            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute($this->isGranted('ROLE_ADMIN') ? 'admin' : 'admin_login');
    }
}