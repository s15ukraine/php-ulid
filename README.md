<h1>Universally Unique Lexicographically Sortable Identifier (ULID)</h1>

UUID can be suboptimal for many use-cases because:

- It isn't the most character efficient way of encoding 128 bits of randomness
- UUID v1/v2 is impractical in many environments, as it requires access to a unique, stable MAC address
- UUID v3/v5 requires a unique seed and produces randomly distributed IDs, which can cause fragmentation in many data structures
- UUID v4 provides no other information than randomness which can cause fragmentation in many data structures

Instead, herein is proposed ULID:

```javascript
ulid() // 01ARZ3NDEKTSV4RRFFQ69G5FAV
```

- 128-bit compatibility with UUID
- 1.21e+24 unique ULIDs per millisecond
- Lexicographically sortable!
- Canonically encoded as a 26 character string, as opposed to the 36 character UUID
- Uses Crockford's base32 for better efficiency and readability (5 bits per character)
- Case insensitive
- No special characters (URL safe)
- Monotonic sort order (correctly detects and handles the same millisecond)

<h2>Specification</h2>

Below is the current specification of ULID as implemented in [ulid/javascript](https://github.com/ulid/javascript).

```
 01AN4Z07BY      79KA1307SR9X4MV3

|----------|    |----------------|
 Timestamp          Randomness
   48bits             80bits
```

<h3>Components</h3>

**Timestamp**
- 48 bit integer
- UNIX-time in milliseconds
- Won't run out of space 'til the year 10889 AD.

**Randomness**
- 80 bits
- Cryptographically secure source of randomness, if possible

<h3>Sorting</h3>

The left-most character must be sorted first, and the right-most character sorted last (lexical order). The default ASCII character set must be used. Within the same millisecond, sort order is not guaranteed

<h3>Canonical String Representation</h3>

```
ttttttttttrrrrrrrrrrrrrrrr

where
t is Timestamp (10 characters)
r is Randomness (16 characters)
```

<h3>Encoding</h3>

Crockford's Base32 is used as shown. This alphabet excludes the letters I, L, O, and U to avoid confusion and abuse.

```
0123456789ABCDEFGHJKMNPQRSTVWXYZ
```

<h2>PHP-ULID</h2>

<h3>Requirements</h3>

PHP 7.4 or greater

<h3>Usage</h3>

Create a brand new ULID

```php
$ulid = ULID::generate();
echo $ulid; // 01FVWA0CN7JGHMPA0NX52ZDZ1B
```

New ULID in lowercase

```php
$ulid = ULID::generate(true);
echo $ulid; // 01fvwa0cn7jghmpa0nx52zdz1b
```

Access timestamp attribute

```php
$timestamp = ULID::decodeTime('01FVWA0CN7JGHMPA0NX52ZDZ1B');
echo $timestamp . ' -> ' . date('d F Y H:i:s', substr($timestamp, 0, 10)); // 1644848755367 -> 14 February 2022 16:25:55
```

<h2>Links And References</h2>

* [Universally Unique Lexicographically Sortable Identifier: The canonical spec for ulid](https://github.com/ulid/spec)
