# MajoraFrameworkExtraBundle
Provides extra classes and config for Symfony framework.
Help to implements a clean and safe DDD architecture.

## Normalize and serialize

Based on Symfony serializer component, MajoraSerializer introduce the scope notion through FormatHandlerInterface.
Using SerializableTrait trait and defining getScope method, you will add to your class an easy way to configure its own serialisation.
Indeed defining ScopableInterface::getScopes() method will allows you to list several normalization strategies.

Let's assume an entity "Person". The Person::getScope method defines 4 different scopes. Scopes can be combined, in this example :

* 'default', is an aggregation of a subset of 'Person' entity attributes and another scope called 'location' defined in the related entity 'related_address'  
* 'full', is an aggregation of 'default' scope and the remaining 'Person' attributes
* 'full_location', is aggregation of 'full' scope and the 'default' scope of 'related_address' entity
* 'id', is a scope which is returning only 'id' attribute

```              
     namespace Alg\Identity\Component\Entity;
     
     use Majora\Framework\Serializer\Model\SerializableTrait;
 
     /**
      * Person model class.
      */
     class Person  
     {
         use SerializableTrait;
         
         // attributes, getters and setters ....
         
         /**
          * @see ScopableInterface::getScopes()
          */
         public static function getScopes()
         {
             return array(
                 'default' => array('id', 'familyName', 'givenName', 'related_address@location'),
                 'full' => array('@default', 'telephone', 'email'),
                 'full_location' => array('@full', 'related_address'),
                 'id' => 'id'
             );
         }
