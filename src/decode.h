#include "hphp/runtime/ext/extension.h"
#include <bson.h>

namespace HPHP {
void bsonToVariant(const bson_t* bson, Array* output);
void bsonToString(bson_iter_t* iter, Array* output);
void bsonToInt32(bson_iter_t* iter, Array* output);
void bsonToInt64(bson_iter_t* iter, Array* output);
void bsonToBool(bson_iter_t* iter, Array* output);
void bsonToNull(bson_iter_t* iter, Array* output);
void bsonToDouble(bson_iter_t* iter, Array* output);
void bsonToArray(bson_iter_t* iter, Array* output, bool isDocument);
void bsonToMongoId(bson_iter_t* iter, Array* output);
void bsonToMongoDate(bson_iter_t* iter, Array* output);
void bsonToMongoRegexp(bson_iter_t* iter, Array* output);
void bsonToMongoTimestamp(bson_iter_t* iter, Array* output);
void bsonToMongoCode(bson_iter_t* iter, Array* output);
void bsonToMongoCodeWithScope(bson_iter_t* iter, Array* output);
void bsonToMongoBinData(bson_iter_t* iter, Array* output);
void bsonToMongoMaxKey(bson_iter_t* iter, Array* output);
void bsonToMongoMinKey(bson_iter_t* iter, Array* output);
}