#include "hphp/runtime/base/base-includes.h"
#include <bson.h>
#include "encode.h"
#include "decode.h"

namespace HPHP {
//////////////////////////////////////////////////////////////////////////////
// functions

static Array HHVM_FUNCTION(bson_decode, const String& bson) {
  bson_reader_t * reader;
  const bson_t * bsonObj;
  bool reached_eof;

  Array output = Array();

  reader = bson_reader_new_from_data((uint8_t *)bson.c_str(), bson.size());
  while ((bsonObj = bson_reader_read(reader, &reached_eof))) {
    bsonToVariant(bsonObj, &output);
  }

  bson_reader_destroy(reader);

  return output;
}

static String HHVM_FUNCTION(bson_encode, const Variant& anything) {
  bson_t bson;
  bson_init(&bson);
        
  fillBSONWithArray(anything.toArray(), &bson);

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
