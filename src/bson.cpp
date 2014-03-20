#include "hphp/runtime/base/base-includes.h"
#include <bson.h>
#include "encode.h"

//#include <stdlib.h>
//#include <stdio.h>

namespace HPHP {
const StaticString s_Mongo("Mongo");

//////////////////////////////////////////////////////////////////////////////
// functions

static Array HHVM_FUNCTION(bson_decode, const String& anything) {
  throw NotImplementedException("bson_decode");
}

static String HHVM_FUNCTION(bson_encode, const Variant& anything) {
  bson_t bson;
  bson_init(&bson);
        
  fillBSONWithArray(anything.toArray(), &bson);

  /*
  char* str = bson_as_json(&bson, NULL);
  fprintf(stdout, "%s\n", str);
  bson_free(str);
  */

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
