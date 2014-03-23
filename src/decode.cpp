#include "hphp/runtime/base/base-includes.h"
#include <bson.h>
#include "decode.h"
#include "classes.h"

namespace HPHP {
void bsonToVariant(const bson_t* bson, Array* output) {
    bson_iter_t iter;
    bson_iter_init(&iter, bson);

    //printf("%s\n", bson_as_json(bson, NULL));
    while (bson_iter_next(&iter)) {
        switch (bson_iter_type(&iter)) {
            case BSON_TYPE_INT32:
                bsonToInt32(&iter, output);
                break;
            case BSON_TYPE_INT64:
                bsonToInt64(&iter, output);
                break;
            case BSON_TYPE_BOOL:
                bsonToBool(&iter, output);
                break;
            case BSON_TYPE_UTF8:
                bsonToString(&iter, output);
                break;
            case BSON_TYPE_NULL:
                bsonToNull(&iter, output);
                break;
            case BSON_TYPE_DOUBLE:
                bsonToDouble(&iter, output);
                break;
            case BSON_TYPE_DOCUMENT:
                bsonToArray(&iter, output, true);
                break;
            case BSON_TYPE_ARRAY:
                bsonToArray(&iter, output, false);
                break;
            case BSON_TYPE_OID:
                bsonToMongoId(&iter, output);
                break;
        }
    }
}

void bsonToMongoId(bson_iter_t* iter, Array* output) {
    const bson_oid_t* oid = bson_iter_oid(iter);
    char id[25];

    bson_oid_to_string(oid, id);
   
    TypedValue ret;
    Class* cls = Unit::loadClass(s_MongoId.get());
    ObjectData* obj = ObjectData::newInstance(cls);

    g_context->invokeFunc(
        &ret, 
        cls->getCtor(), 
        make_packed_array(String(id)),
        obj
    );
    
    printf("After: %s\n", id);

    output->add(
        String(bson_iter_key(iter)),
        obj
    ); 
}



void bsonToArray(bson_iter_t* iter, Array* output, bool isDocument) {
    bson_t bson;
    const uint8_t *document = NULL;
    uint32_t document_len = 0;

    if (isDocument) {
        bson_iter_document(iter, &document_len, &document);
    } else {
        bson_iter_array(iter, &document_len, &document);
    }

    bson_init_static(&bson, document, document_len);

    Array child = Array();
    bsonToVariant(&bson, &child);

    output->add(
        String(bson_iter_key(iter)),
        child
    );   
}

void bsonToString(bson_iter_t* iter, Array* output) {
    output->add(
        String(bson_iter_key(iter)),
        String(bson_iter_utf8(iter, NULL))
    ); 
}

void bsonToInt32(bson_iter_t* iter, Array* output) {
    output->add(
        String(bson_iter_key(iter)),
        bson_iter_int32(iter)
    ); 
}

void bsonToInt64(bson_iter_t* iter, Array* output) {
    output->add(
        String(bson_iter_key(iter)),
        bson_iter_int64(iter)
    ); 
}

void bsonToBool(bson_iter_t* iter, Array* output) {
    output->add(
        String(bson_iter_key(iter)),
        bson_iter_bool(iter)
    ); 
}

void bsonToNull(bson_iter_t* iter, Array* output) {
    output->add(
        String(bson_iter_key(iter)),
        Variant()
    ); 
}

void bsonToDouble(bson_iter_t* iter, Array* output) {
    output->add(
        String(bson_iter_key(iter)),
        bson_iter_double(iter)
    ); 
}

}