#include "hphp/runtime/base/base-includes.h"
#include <bson.h>
#include <stdlib.h>
#include <stdio.h>

namespace HPHP {
const StaticString s_Mongo("Mongo");

//////////////////////////////////////////////////////////////////////////////
// functions

static Array HHVM_FUNCTION(bson_decode, const String& anything) {
  throw NotImplementedException("bson_decode");
}


static void stringToBSON(const String& value, bson_t* bson) {
    //bson_append_utf8(bson, "2", -1, 3);
}

static void arrayToBSON(const Array& value, bson_t* bson) {
	for (ArrayIter it(value); it; ++it) {
		Variant key = it.first();
		if (!key.isNumeric()) {
    	}
  	}


}

static void variantToBSON(const Variant& value, bson_t* bson) {
    switch(value.getType()) {
	    case KindOfUninit:
        case KindOfNull:
            printf("Null: %d ", value.getType());
            break;
        case KindOfBoolean:
            printf("Boolean: %d ", value.getType());
            break;
        case KindOfInt64:
            printf("Int64: %d ", value.getType());
            break;
        case KindOfDouble:
            printf("Double: %d ", value.getType());
            break;
        case KindOfStaticString:
        case KindOfString:
            stringToBSON(value.toString(), bson);
            printf("String: %d ", value.getType());
            break;
        case KindOfArray:
            arrayToBSON(value.toArray(), bson);
            printf("Array: %d ", value.getType());
            break;	
        default:
			printf("NotImplemented: %d ", value.getType());
	}
}

static String HHVM_FUNCTION(bson_encode, const Variant& anything) {
	bson_t bson, foo, bar, baz;
	bson_init(&bson);
        
	variantToBSON(anything, &bson);

	bson_append_document_begin(&bson, "foo", -1, &foo);
    bson_append_document_begin(&foo, "bar", -1, &bar);
    bson_append_array_begin(&bar, "baz", -1, &baz);
    bson_append_int32(&baz, "0", -1, 1);
    bson_append_int32(&baz, "1", -1, 2);
    bson_append_int32(&baz, "2", -1, 3);
    bson_append_array_end(&bar, &baz);
    bson_append_document_end(&foo, &bar);
    bson_append_document_end(&bson, &foo);

    char* str = bson_as_json(&bson, NULL);
    fprintf(stdout, "%s\n", str);
    bson_free(str);

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
