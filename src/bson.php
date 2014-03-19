<?hh
/**
 * Deserializes a BSON object into a PHP array
 *
 * @param string $bson - The BSON to be deserialized.
 *
 * @return array - Returns the deserialized BSON object.
 */
<<__Native>>
function bson_decode(string $bson): array;

/**
 * Serializes a PHP variable into a BSON string
 *
 * @param mixed $anything - The variable to be serialized.
 *
 * @return string - Returns the serialized string.
 */
<<__Native>>
function bson_encode(mixed $anything): string;
