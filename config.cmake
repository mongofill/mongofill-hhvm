FIND_PATH(BSON_INCLUDE_DIR NAMES bson.h
    PATHS /usr/include /usr/include/libbson-1.0 /usr/local/include /usr/local/include/libbson-1.0)

FIND_LIBRARY(BSON_LIBRARY NAMES bson-1.0 PATHS /lib /usr/lib /usr/local/lib)

IF (BSON_INCLUDE_DIR AND BSON_LIBRARY)
    MESSAGE(STATUS "bson Include dir: ${BSON_INCLUDE_DIR}")
    MESSAGE(STATUS "libbson library: ${BSON_LIBRARY}")
ELSE()
    MESSAGE(FATAL_ERROR "Cannot find libbson library")
ENDIF()

include_directories(${BSON_INCLUDE_DIR})

HHVM_EXTENSION(bson src/bson.cpp src/encode.cpp)
HHVM_SYSTEMLIB(bson src/bson.php)

target_link_libraries(bson ${BSON_LIBRARY})

