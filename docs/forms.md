# Forms

The Symfony Form component has a powerfull extension system, and allows us to customize it with extra behaviors.

All this stuff is non-intrusive in your project and have to be activated through bundle config, and a form option.

## JSON extension

By default, Form component can handle HttpKernel request, but only if data are sent in `GET`, or in `POST` (`PUT` / `PATCH`) with `application/x-www-form-urlencoded` or `multipart/form-data` encoded data.

No Json. With current microservices / REST / cloud mania. Mmmmkay !

Json extension allows Form component to parse directly json into request content, and uses it like row data.

Step 1 : [Install](installation.md) the bundle.

Step 2 : Enable Json extension into your config.yml file
```yml
# app/config/config.yml

majora_framework_extra:
    # ...
    json_form_extension:
        enabled: true
```

Step 3 : Use dedicated option into your form
```php
public function postArticleAction(Request $request)
{
    $form = $this->container->get('form.factory')
        ->createNamed('', ArticleCreationType::class, null, array(
            'method' => 'POST',
            'csrf_protection' => false,
            'json_format' => true
        ))
    );

    $form->handleRequest($request);
    if ($form->isValid()) {
        // ......
    }
}
```

Step 4 : Test it !
```
POST /articles

Content-Type application/json;charset=UTF-8
# ....


{"title":"MajoraFrameworkExtraBundle documentation released","headline":"More than a year after release, MajoraFramework got a proper documentation !":"body":"That\'s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !"}
```

Form component will validate and map all this json data as if they were sent in x-www-form-urlencoded.

**Credits** :

 - [Morgan](https://github.com/holoflins)
 - [Raphaël](https://github.com/raphael-trzpit)
 - [Valentin](https://github.com/ValentinCoulon)

## Loader collections bridge

With Majora Framework, we got services which can load our entities in different ways, even in form types.
Like Doctrine entity form type, this custom entity type aim to extend `choiceType` to work with Majora loaders.

First of all, create you loader and add an __alias__ on it
```xml
<service id="demo.entity_collection_type.loader.user" class="Demo\EntityCollectionTypeBundle\Loader\UserLoader">
    <argument type="service" id="demo.entity_collection_type.repository.user" />
    <tag name="majora.loader"
        alias="demo.user.loader"
        entity="Demo\EntityCollectionTypeBundle\Model\User"
        collection="Demo\EntityCollectionTypeBundle\Collection\UserCollection"
    />
</service>
```

Use the new form type, with the required option `loader_alias` :
```php
// ...
use Majora\Framework\Loader\Bridge\Form\Type\EntityCollectionType;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityCollectionType::class, array(
                'label' => 'Utilisateurs',
                'loader_alias' => 'demo.user.loader',
            ))
        ;
    }
}
```
The form type allow you to define which method should be use on the loader to retrieve data with the option `loader_method`.
This option use by default a method named `choiceList`. 
The `loader_method` will have to return an `EntityCollection`.

**Credits** :

 - [Jamal](https://github.com/jamyouss)

## Translatable text

Work in progress !
