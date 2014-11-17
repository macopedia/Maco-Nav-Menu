Macopedia Easymenu
==================

Navigation menu for Magento configurable from the backend (admin panel).

If you set the cache lifetime for a block, e.g.
```
<reference name="easymenu">
    <action method="setCacheLifetime"><value>43200</value></action>
</reference>
```

Generated menu will not have an "current-page" class for the anhor.
As we don't want it to be cached.
You can still add this class by some little js magic:

```
$('#nav a').each(function () {
    var self = $(this);
    if (window.location.href === self.attr('href')) {
        self.addClass('current-page');
    }
});
```
