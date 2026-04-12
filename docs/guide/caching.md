# Caching

Caching can significantly improve performance when rendering the same content repeatedly. The bundle integrates with Symfony's cache component.

## Enable Caching

```yaml
# config/packages/symfony_djot.yaml
symfony_djot:
    cache:
        enabled: true
        pool: cache.app    # Uses Symfony's default cache pool
```

## How It Works

1. Content is hashed using xxHash (fast, collision-resistant)
2. Rendered HTML is stored in the cache pool
3. Subsequent renders of identical content return cached HTML
4. Cache keys are prefixed with `symfony_djot_html_`

## Cache Pools

Use any Symfony cache pool:

```yaml
# config/packages/cache.yaml
framework:
    cache:
        pools:
            cache.djot:
                adapter: cache.adapter.redis
                default_lifetime: 3600

# config/packages/symfony_djot.yaml
symfony_djot:
    cache:
        enabled: true
        pool: cache.djot
```

### Common Pool Configurations

**Redis (recommended for production):**
```yaml
framework:
    cache:
        pools:
            cache.djot:
                adapter: cache.adapter.redis
                provider: 'redis://localhost'
```

**Filesystem (development):**
```yaml
framework:
    cache:
        pools:
            cache.djot:
                adapter: cache.adapter.filesystem
                directory: '%kernel.cache_dir%/djot'
```

**APCu (single server):**
```yaml
framework:
    cache:
        pools:
            cache.djot:
                adapter: cache.adapter.apcu
```

## Cache Invalidation

The cache is keyed by content hash, so:

- **Changed content** automatically gets a new cache entry
- **Old entries** expire based on the pool's TTL
- **Manual clearing** via Symfony's cache:clear command

```bash
# Clear all caches
php bin/console cache:pool:clear cache.djot

# Or clear everything
php bin/console cache:clear
```

## When to Use Caching

**Good candidates:**
- Static content (about pages, documentation)
- Blog posts that don't change often
- Repeated rendering of the same content

**Skip caching for:**
- User-specific content with variables
- Rapidly changing content
- Very short content (overhead > benefit)

## Performance Considerations

| Scenario | Recommendation |
|----------|----------------|
| Blog with 100 posts | Enable caching with Redis |
| User comments | Usually skip (too many unique entries) |
| Documentation site | Enable with filesystem cache |
| Real-time editor preview | Disable caching |

## Measuring Impact

Enable Symfony's profiler to see cache hit/miss rates:

```yaml
# config/packages/dev/cache.yaml
framework:
    cache:
        pools:
            cache.djot:
                adapter: cache.adapter.filesystem
                tags: true  # Enables tagging/tracking
```

Check the profiler's Cache panel to see `symfony_djot_html_*` entries.

## Next Steps

- [Configuration](configuration.md) — full configuration reference
- [Service Usage](service-usage.md) — using the converter in code
