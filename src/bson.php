#include "hphp/runtime/base/base-includes.h"
namespace HPHP {
const StaticString s_Mongo("Mongo");

//////////////////////////////////////////////////////////////////////////////
// functions

static Array HHVM_FUNCTION(bson_decode, const String& bson) {
  throw NotImplementedException("bson_decode");
}

static String HHVM_FUNCTION(bson_encode, const Variant& anything) {
  throw NotImplementedException("bson_encode");
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
//HHVM_GET_MODULE(mongo);

//////////////////////////////////////////////////////////////////////////////
} // namespace HPHP
