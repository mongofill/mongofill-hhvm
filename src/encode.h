#include "hphp/runtime/base/base-includes.h"
#include <bson.h>

namespace HPHP {
bool arrayIsDocument(const Array& arr);
void fillBSONWithArray(const Array& value, bson_t* bson);
void stringToBSON(const String& value, const char* key, bson_t* bson);
void arrayToBSON(const Array& value, const char* key, bson_t* bson);
void variantToBSON(const Variant& value, const char* key, bson_t* bson);
void int64ToBSON(const int64_t value, const char* key, bson_t* bson);
void boolToBSON(const bool value, const char* key, bson_t* bson);
void nullToBSON(const char* key, bson_t* bson);
void doubleToBSON(const double value,const char* key, bson_t* bson);
}