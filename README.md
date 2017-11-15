# UUID performance tests

This playground provides a bunch of crude benchmark tests to test the performance of MySQL queries with UUIDs in various scenarios.

Read more about it here: [http://mysqlserverteam.com/storing-uuid-values-in-mysql-tables/](http://mysqlserverteam.com/storing-uuid-values-in-mysql-tables/).

## Setup

Please run `composer install` and setup a local MySQL database. Than copy `.env.exmaple` to `.env` and fill in your credentials in `.env`.

Note that you can set a few configuration parameters in `.env` to manipulate the benchmark results.

### First run

`console.php` is used to run the benchmarks, the first time around, you'll want to run the benchmark command with the `--table` option.
This option will drop the current tables and start over again. 
Be aware that adding the `--table` option may cause the benchmark to take a lot longer to finish, depending on the amount of rows you're adding.

## Benchmarks

Run the benchmarks with the following command. You'll probably want to keep the memory_limit option, if you're setting a higher `FLUSH_QUERY_AMOUNT`.
This can improve the speed at which the benchmarks are run.

```
php -d memory_limit=-1 console.php benchmark [--table]
```

### The `Normal ID` benchmark

This is the baseline benchmark, executing `SELECT` queries based on a normal `AUTO_INCREMENT` id.

### The `Binary UUID` benchmark

This benchmark will run `SELECT` queries on a table with a binary encoded `UUID` field as its primary key. 
It's input is a normal UUID which is encoded in the query to its binary variant.

This benchmark seems to be the closest in performance to the `Normal ID` benchmark, with less then 500k records in a table.

```
- Normal ID:
    Avarage of 0.056232ms over 10000 iterations.
- Binary UUID:
    Avarage of 0.078953ms over 10000 iterations.
- Optimised UUID:
    Avarage of 0.08929ms over 10000 iterations.
```

*- Results when querying a small amount of records (~10k).*

### The `Optimised UUID` and `Optimised UUID from text`

These benchmarks query an optimised table containing a bit-shuffled version of a `UUID`.
Shuffling the bits results in better performance compared to the normal `Binary UUID` approach, only when there's 
a lot of records in a table (>500k on my local machine).

The `Optimised UUID from text` benchmark is differs from the `Optimised UUID` in its input method. 
`Optimised UUID from text` represents the way client input would be sent to your backend application. 

Comparing results, there's no difference between the two.

```
- Binary UUID:
    Avarage of 0.192199ms over 10000 iterations.
- Optimised UUID:
    Avarage of 0.120646ms over 10000 iterations.
- Optimised UUID from text:
    Avarage of 0.109521ms over 10000 iterations.
```

*- The difference between `Binary UUID` and `Optimised UUID` when querying a big dataset of ~500k records.*

### The `Textual UUID` benchmark

This benchmark represents the normal approach to `UUID`s, storing them as `VARCHAR(36)`. 
You'll immediately notice that this benchmark is much slower than the others, even with small amounts of data.

```
- Normal ID:
    Avarage of 0.072008ms over 10000 iterations.
- Binary UUID:
    Avarage of 0.113014ms over 10000 iterations.
- Optimised UUID:
    Avarage of 0.11051ms over 10000 iterations.
- Optimised UUID from text:
    Avarage of 0.105908ms over 10000 iterations.
- Textual UUID:
    Avarage of 96.298254ms over 100 iterations.
```


## Conclusions

These are by no means accurate benchmarks when measuring the actual performance of queries. 
They do however allow for a relative comparison between each approach. 

It is clear that querying a `UUID` stored as a `VARCHAR(36)` is painfully slow compared to the other ways of storing them.
There's little to no difference between `Binary UUID` and `Optimised UUID` when querying smaller tables.

The question we wanted answering was how we could store a large amount of data (>500k) the best way, when using `UUID`s.
For us, this benchmark answered that question.
