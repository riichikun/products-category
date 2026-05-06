<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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
use BaksDev\Products\Category\Entity as CategoryEntity;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Products\Category\Type\Parent\ParentCategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductForm;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_PRODUCT_CATEGORY_NEW')]
final class NewController extends AbstractController
{
    #[Route(
        '/admin/product/category/new/{cat}/{id}',
        name: 'admin.newedit.new',
        defaults: ['cat' => null, 'id' => null],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryProductHandler $handler,
        #[MapEntity] ?CategoryEntity\CategoryProduct $cat = null,
        ?CategoryProductEventUid $id = null,
    ): Response
    {
        $parent = $cat ? new ParentCategoryProductUid($cat->getId()) : null;
        $Event = $id ? $entityManager->getRepository(CategoryEntity\Event\CategoryProductEvent::class)
            ->find($id) : null;

        $CategoryProductDTO = new CategoryProductDTO($parent);

        // Копируем данные из события
        if($Event)
        {
            $Event->getDto($CategoryProductDTO);
        }

        // Форма добавления
        $form = $this
            ->createForm(CategoryProductForm::class, $CategoryProductDTO)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('Save'))
        {
            $this->refreshTokenForm($form);

            $ProductCategory = $handler->handle($CategoryProductDTO);

            if(true === $ProductCategory)
            {
                $this->addFlash('success', 'admin.success.new', 'admin.products.category');

                return $this->redirectToRoute('products-category:admin.index');
            }

            $this->addFlash('danger', 'admin.danger.new', 'admin.products.category', $ProductCategory);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
