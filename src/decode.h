#include "hphp/runtime/base/base-includes.h"
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
}