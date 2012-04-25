#ifndef DEFINES_H
#define DEFINES_H

/**
  Exit codes:
  ----------------
  0: Failure
  1: Success
  2: Success, unkown params ignored
  10: Failed, missing fileid
  11: Failed, missing filename
  12: Failed, missing type
  20: Unknown file type
  21: File not found
  22: Invalid file, or wrongly formatted
  30: Could not read input file
  31: Could not write to input file
  32: Could not read output file
  33: Could not write to output file
  34: Could not delete output file
  **/

#define FAILURE 0
#define SUCCESS 1
#define SUCCES_UNK_PARAM_IGN 2
#define MISS_FILEID 10
#define MISS_FILENAME 11
#define MISS_TYPE 12
#define UNK_FILE_TYPE 20
#define FILE_NOT_FOUND 21
#define INVALID_FILE 22
#define COULD_NOT_READ_IN_FILE 30
#define COULD_NOT_WRITE_IN_FILE 31
#define COULD_NOT_WRITE_OUT_FILE 32
#define COULD_NOT_READ_OUT_FILE 33
#define COULD_NOT_DELETE_OUT_FILE 34

#endif // DEFINES_H
