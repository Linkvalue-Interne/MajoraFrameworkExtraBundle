# Dependency injection helpers

Symfony DependencyInjection component is really huge. All features an architect dreamed about are in there.
Still, it miss some little features, especially when working a complex service structure.

## Aliases

Aliases are a great feature to implement service behavior abstraction, like choose one interface implementation or another from configurations.
Sometimes you want to define a default one, from a bundle, et override it in another bundle. But doing so, you rely on bundle inclusion order, which is really not advised.
So you have to define your aliases in a CompilerPassInterface (or Extension since Symfony 2.8), but it require many lines of complex concepts just for an alias.

MajoraFramework adds a simple service tag, `majora.alias`, which can be use into all your services to define an alias of current service, included by an internal CompilerPass.

```xml
<service id="majora.twitter.http_client" class="MajoraTwitterBundle/Twitter/TwitterApiClient">
    <tag name="majora.alias" alis="majora.twitter.api_client" />
</service>
```

That's all, no CompilerPass to implement yourself, and no tight coupling with bundle inclusion order.
