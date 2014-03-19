#include "hphp/runtime/base/base-includes.h"
#include <bson.h>
#include <stdlib.h>
#include <stdio.h>

namespace HPHP {
const StaticString s_Mongo("Mongo");

//////////////////////////////////////////////////////////////////////////////
// functions

static Array HHVM_FUNCTION(bson_decode, const Variant& anything) {
  throw NotImplementedException("bson_decode");
}

static String HHVM_FUNCTION(bson_encode, const String& test) {
	bson_t bson, foo, bar, baz;
	bson_init(&bson);
        
	bson_append_document_begin(&bson, "foo", -1, &foo);
        bson_append_document_begin(&foo, "bar", -1, &bar);
        bson_append_array_begin(&bar, "baz", -1, &baz);
        bson_append_int32(&baz, "0", -1, 1);
        bson_append_int32(&baz, "1", -1, 2);
        bson_append_int32(&baz, "2", -1, 3);
        bson_append_array_end(&bar, &baz);
        bson_append_document_end(&foo, &bar);
        bson_append_document_end(&bson, &foo);

	const char* output = (const char*) bson_get_data(&bson);
				
	return String(output, bson.len, CopyString);
}

//////////////////////////////////////////////////////////////////////////////

class mongoExtension : public Extension {
 public:
  mongoExtension() : Extension("mongo") {}
  virtual void moduleInit() {
    HHVM_FE(bson_decode);
    HHVM_FE(bson_encode);
    loadSystemlib();
  }
} s_mongo_extension;

// Uncomment for non-bundled module
HHVM_GET_MODULE(mongo);

//////////////////////////////////////////////////////////////////////////////
} // namespace HPHP
