<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Products\Category\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Repository\ProjectProfileLandings\ProjectProfileLandingsInterface;
use BaksDev\Products\Category\Repository\ProjectProfileLandings\ProjectProfileLandingsResult;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductForm;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductHandler;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Project\Landing\CategoryProductProjectLandingDTO;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_PRODUCT_CATEGORY_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/product/category/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] Entity\Event\CategoryProductEvent $Event,
        CategoryProductHandler $handler,
        ProjectProfileLandingsInterface $profileLandings,
    ): Response
    {
        /** @var CategoryProductDTO $ProductCategoryDTO */
        $ProductCategoryDTO = $Event->getDto(CategoryProductDTO::class);

        /* Получить ProjectProfileLandings */

        $projectProfileLandings = $profileLandings
            ->byCategory($Event->getMain())
            ->findAll();


        /* Задать значения для bottom и header */
        if(false !== $projectProfileLandings)
        {
            $descriptionCollection = $ProductCategoryDTO->getProject()->getLanding();

            /** @var ProjectProfileLandingsResult $projectProfileLanding */
            foreach($projectProfileLandings as $projectProfileLanding)
            {
                /** @var CategoryProductProjectLandingDTO $CategoryProductProjectLandingDTO */
                foreach($descriptionCollection as $CategoryProductProjectLandingDTO)
                {
                    /* Если совпадают значения по 'local' и 'device', задать значения */
                    if(
                        $projectProfileLanding->getLocal()->equals($CategoryProductProjectLandingDTO->getLocal())
                        && $projectProfileLanding->getDevice()->equals($CategoryProductProjectLandingDTO->getDevice())
                    )
                    {
                        $CategoryProductProjectLandingDTO->setBottom($projectProfileLanding->getBottom());
                        $CategoryProductProjectLandingDTO->setHeader($projectProfileLanding->getHeader());
                    }
                }
            }
        }


        // Форма добавления
        $form = $this->createForm(CategoryProductForm::class, $ProductCategoryDTO);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('Save'))
        {
            $this->refreshTokenForm($form);

            $ProductCategory = $handler->handle($ProductCategoryDTO);

            if($ProductCategory === true)
            {
                $this->addFlash('success', 'admin.success.update', 'admin.products.category');

                return $this->redirectToRoute('products-category:admin.index');
            }

            $this->addFlash('danger', 'admin.danger.update', 'admin.products.category', $ProductCategory);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
