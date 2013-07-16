<?php

namespace Ibrows\SonataTranslationBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

use Sonata\AdminBundle\Controller\CRUDController;

class TranslationCRUDController extends CRUDController
{

    /**
     * @param string $id
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\Response|Ambigous <\Symfony\Component\HttpFoundation\Response, \Symfony\Component\HttpFoundation\RedirectResponse>
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        if (!$request->isMethod('POST')) {
            return $this->redirect($this->admin->generateUrl('list'));
        }
        
        /* @var $transUnit \Lexik\Bundle\TranslationBundle\Model\TransUnit */
        $transUnit = $this->admin->getObject($id);
        if (!$transUnit) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        if (false === $this->admin->isGranted('EDIT', $transUnit)) {
            return $this->renderJson(array(
                    'message' => 'access denied'
            ), 403);
        }

        $this->admin->setSubject($transUnit);
        
        /* @var $transUnitManager \Lexik\Bundle\TranslationBundle\Manager\TransUnitManager */
        $transUnitManager = $this->get('lexik_translation.trans_unit.manager');
        $parameters = $this->getRequest()->request;

        $locale = $parameters->get('locale');
        $content = $parameters->get('value');
        
        if (!$locale) {
            return $this->renderJson(array(
                    'message' => 'locale missing'
            ), 422);
        }
        
        /* @var $translation \Lexik\Bundle\TranslationBundle\Model\Translation */
        if ($parameters->get('pk')) {
            $translation = $transUnitManager->updateTranslation($transUnit, $locale, $content, true);
        } else {
            $translation = $transUnitManager->addTranslation($transUnit, $locale, $content, null, true);
        }
        
        return $this->renderJson(array(
                'key' => $transUnit->getKey(),
                'domain' => $transUnit->getDomain(),
                'pk' => $translation->getId(),
                'locale' => $translation->getLocale(),
                'value' => $translation->getContent()
        ));
    }

    public function clearCacheAction()
    {

        $this->get('translator')->removeLocalesCacheFiles($this->getManagedLocales());
        
        /** @var $session Session */
        $session = $this->get('session');
        $session->getFlashBag()->set('sonata_flash_success', 'translations.cache_removed');
        
        return $this->redirect($this->admin->generateUrl('list'));
    }
    
    protected function getManagedLocales()
    {
        return $this->container->getParameter('lexik_translation.managed_locales');
    }
}
