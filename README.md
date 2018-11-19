Tento repozitář obsahuje demonstrační příklady pro workshop o asynchronním
zpracování v PHP.

Náš problém je následující. Vyhledejte všechny odkazy na zadané stránce. Pokud
je nalezený odkaz URL na další stránku vyhledejte odkazy i na ní, atd. až do
zadané hloubky.

## Instalace
Příklady vyžadují [docker-compose] pro spuštění Redisu a workerů, dále poskytuje
PHP pro spouštění web crawleru.

Stáhněte zdrojové kódy
```
git clone https://github.com/intraworlds/workshop-async-php.git
cd workshop-async-php
```

Nainstalujte závislosti
```
docker-compose run --rm php -r "copy('https://getcomposer.org/composer.phar', 'composer.phar');"
docker-compose run --rm php php composer.phar install
```

Spusťte Redis a workera
```
docker-compose up -d
```

## Příklady
Applikace dostává tři parametry
```
crawl.php <type> <url> <depth>
```

Vrať všechny odkazy na stránce `www.intraworlds.com`
```
docker-compose run -T php bin/crawl.php sync https://www.intraworlds.com 0
```

Vrať všecny odkazy na stránce `www.intraworlds.cz` a odkazy na nich
```
docker-compose run -T php bin/crawl.php sync https://www.intraworlds.cz 1
```

> Poror! Opatrně s hloubkou prohledávání. Hodnoty nad 1 jdou už do tisíců odkazů

## Úkoly
1. upravte kód workeru tak, aby načítal pouze unikátní odkazy (viz typ `unique`),
tip: využijte Redis, viz náš [předchozí workshop], pozor na souběhy v případě
více workerů
1. odkomentujte řádek v souboru `bin/worker.php`, worker začne s 50% pravděpodobností
padat před vykonáním práce (dochází ke zdrátě dat z fronty `queue`), upravte kód
tak aby práce nebyla ztracena

> Rada: při změně kódů workeru je nutné worker restartovat pomocí
`docker-compose restart worker`

> Tip: Prostudujte Redis command [RPOPLPUSH]

## Bonusové úkoly
1. upravte kód typu promis tak, aby vracel iterátor místo pole (výsledky budou
ihned vidět)
1. naimplementujte nový typ s použitím knihovny [amphp/parallel]
1. naimplementujte nový typ s použitím knihovny [react/react] nebo [amphp/amp]
1. naimplementujte nový typ s použitím rozšíření [pthreads]

## Odevzdání
- vytvořením pull requestu do tohoto repozitáře

nebo

- zasláním na emailovou adresu workshop@intraworlds.com

## Řešení
Řešení úkolů a mnoho dalších informací k dispozici na našem IWorkoshopu 27.11.2018
od 18 hodin v Beer Factory. Přijďte, vstup je **zdarma**!

## Typy
- `sync` normální synchronní prohlédávání, stránky se načítají jedna za druhou
- `unique` stejné jako `sync`, ale prohledává jen unikátní odkazy
- `worker` jednotlivé stránky jsou zpracovány asynchronně v samostaném workeru,
počet workerů je možné měnit a tím ovlivňovat rychlost prohledávání, zkuste
`docker-compose up -d --scale worker=3` viz náš [první workshop]
- `promise` využívá asynchronní HTTP request (pomocí Guzzle). Odešle všechny
dílčí requesty najednou a poté postupně čeká na jejich výsledek.


[docker-compose]: https://docs.docker.com/compose/
[amphp/parallel]: https://packagist.org/packages/amphp/parallel
[react/react]: https://packagist.org/packages/react/react
[amphp/amp]: https://packagist.org/packages/amphp/amp
[první workshop]: https://www.intraworlds.cz/wp-content/uploads/2018/01/IntraWorlds_Zadani_ulohy.pdf
[předchozí workshop]: https://github.com/intraworlds/workshop-redis
[pthreads]: https://secure.php.net/manual/en/book.pthreads.php
[RPOPLPUSH]: https://redis.io/commands/rpoplpush
