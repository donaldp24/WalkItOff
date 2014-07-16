//
//  Database+walkitoff.m
//  WalkItOff
//
//  Created by Donald Pae on 7/3/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "Database+walkitoff.h"
#import "CommonMethods.h"
#import "NSDate+walkitoff.h"

static Database *_sharedDatabase = nil;

@implementation Database

+ (Database *)sharedDatabase
{
    if (_sharedDatabase == nil)
        _sharedDatabase = [[Database alloc] init];
    return _sharedDatabase;
}

- (id)init
{
    self = [super init];
    
    if (self) {
        NSString *docsDir;
        NSArray *dirPaths;
        
        // Get the documents directory
        dirPaths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
        docsDir = [dirPaths objectAtIndex:0];
        // Build the path to the database file
        NSString *databasePath = [[NSString alloc] initWithString: [docsDir stringByAppendingPathComponent: DATABASE_FILENAME]];
        
        NSFileManager *filemgr = [NSFileManager defaultManager];
        //[filemgr removeItemAtPath:databasePath error:nil];
        BOOL isDbExist = [filemgr fileExistsAtPath:databasePath];
        _fmdb = [[FMDatabase alloc] initWithPath:databasePath];
        
        //if (isDbExist)
            [_fmdb open];
        
        BOOL isDbCreate = NO;
        if (isDbExist == NO)
        {
            isDbCreate = YES;
        }
        else
        {
            NSString *currVersion = @"0";
            FMResultSet *results = [_fmdb executeQuery:@"SELECT * FROM tbl_version"];
            while ([results next]) {
                currVersion = [results stringForColumn:@"version_no"];
                break;
            }
            if ([currVersion isEqualToString:@"1"]) {
                // delete original db
                /*
                 [_database close];
                 [filemgr removeItemAtPath:databasePath error:nil];
                 [_database open];
                 isDbCreate = YES;
                 */
            }
        }
        
        if (isDbCreate)
        {
            char *sql_customfood = "CREATE TABLE tbl_customfood(uid INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, calories DOUBLE, useruid INTEGER, createdtime TEXT, deleted INTEGER)";
         
            char *sql_current = "CREATE TABLE tbl_currentfood(uid INTEGER PRIMARY KEY AUTOINCREMENT, fooduid INTEGER, brand TEXT, name TEXT, servingsize TEXT, calories DOUBLE, protein TEXT, carbs TEXT, fat TEXT, image TEXT, createdtime TEXT, useruid INTEGER, iscustom INTEGER, consumedtime TEXT, isconsumed INTEGER, deleted INTEGER)";
            
            char *sql_favorites = "CREATE TABLE tbl_favoritefood(uid INTEGER PRIMARY KEY AUTOINCREMENT, fooduid INTEGER, brand TEXT, name TEXT, servingsize TEXT, calories DOUBLE, protein TEXT, carbs TEXT, fat TEXT, image TEXT, createdtime TEXT, useruid INTEGER, iscustom INTEGER, deleted INTEGER)";
            
            char *sql_consumed = "CREATE TABLE tbl_consumed(uid INTEGER PRIMARY KEY AUTOINCREMENT, useruid INTEGER, consumeddate TEXT, createdtime TEXT, stepstaken INTEGER, caloriesconsumed DOUBLE, mileswalked DOUBLE, deleted INTEGER)";
            
            char *sql_version = "CREATE TABLE tbl_version(version_id INTEGER PRIMARY KEY AUTOINCREMENT, version_no TEXT);";
            
            char *sql_operation = "CREATE TABLE tbl_operation(uid INTEGER PRIMARY KEY AUTOINCREMENT, useruid INTEGER, type INTEGER, timestamp TEXT, synced INTEGER, synceddate TEXT)";
            
            char *sql_operationparams = "CREATE TABLE tbl_operationparams(uid INTEGER PRIMARY KEY AUTOINCREMENT, operationuid INTEGER, name TEXT, val TEXT)";
            
            BOOL bRet;
            bRet = [_fmdb executeDDL:sql_customfood];
            bRet = [_fmdb executeDDL:sql_current];
            bRet = [_fmdb executeDDL:sql_favorites];
            bRet = [_fmdb executeDDL:sql_consumed];
            bRet = [_fmdb executeDDL:sql_version];
            bRet = [_fmdb executeDDL:sql_operation];
            bRet = [_fmdb executeDDL:sql_operationparams];
            
            NSString *sql_version_register = [NSString stringWithFormat:@"INSERT INTO tbl_version(version_no) VALUES('%@')", DATABASE_VERSION];
            bRet = [_fmdb executeUpdate:sql_version_register];
            if (bRet == NO)
                bRet = bRet;
        }
    }
    
    return self;
}

#pragma mark - Custom food

- (void)addCustomFood:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *strDate = [CommonMethods date2str:[NSDate date] withFormat:DATETIME_FORMAT];
    NSString *insertSql = [NSString stringWithFormat:@"INSERT INTO tbl_customfood(name, calories, useruid, createdtime, deleted) VALUES('%@', %f, %d, '%@', 0)",
                           food.name,
                           food.calories,
                           useruid,
                           strDate];
    BOOL bRet = [_fmdb executeUpdate:insertSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", insertSql]);
    }
}

- (void)removeCustomFood:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *updateSql = [NSString stringWithFormat:@"UPDATE tbl_customfood SET deleted = 1 WHERE uid = %d AND deleted = 0", food.uid];
    BOOL bRet = [_fmdb executeUpdate:updateSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", updateSql]);
    }
}

- (void)getCustomFoods:(int)useruid keyword:(NSString *)keyword success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    NSString *selectSql = [NSString stringWithFormat:@"SELECT * FROM tbl_customfood WHERE deleted = 0 AND useruid = %d AND name like '%%%@%%'", useruid, keyword];
    
    FMResultSet *results = [_fmdb executeQuery:selectSql];
    if (results)
    {
        NSMutableArray *foods = [[NSMutableArray alloc] init];
        while ([results next]) {
            Food *food = [[Food alloc] init];
            
            food.isCustom = 1;
            food.uid = [results intForColumn:@"uid"];
            food.name = [results stringForColumn:@"name"];
            food.calories = [results doubleForColumn:@"calories"];
            
            [foods addObject:food];
        }
        success(foods);
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", selectSql]);
    }
}


#pragma mark - Current foods

- (void)addFoodToCurrent:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *strDate = [CommonMethods date2str:[NSDate date] withFormat:DATETIME_FORMAT];
    NSString *insertSql = [NSString stringWithFormat:@"INSERT INTO tbl_currentfood(fooduid,  brand, name, servingsize, calories, protein, carbs, fat, image, createdtime, useruid, iscustom, consumedtime, isconsumed, deleted) VALUES(%d, '%@', '%@', '%@', %f, '%@', '%@', '%@', '%@', '%@', %d, %d, '%@', 0, 0)",
                           food.uid,
                           food.brand,
                           food.name,
                           food.servingsize,
                           food.calories,
                           food.protein,
                           food.carbs,
                           food.fat,
                           food.image,
                           strDate,
                           useruid,
                           food.isCustom,
                           strDate];
    BOOL bRet = [_fmdb executeUpdate:insertSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", insertSql]);
    }
}

- (void)removeFoodFromCurrent:(int)useruid currentFood:(CurrentFood *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *updateSql = [NSString stringWithFormat:@"UPDATE tbl_currentfood SET deleted = 1 WHERE uid = %d", food.currentUid];
    BOOL bRet = [_fmdb executeUpdate:updateSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", updateSql]);
    }
}


- (void)getCurrentFoods:(int)useruid success:(void (^)(NSMutableArray *foods))success failure:(void (^)(NSString *))failure
{
    NSString *selectSql = [NSString stringWithFormat:@"SELECT * FROM tbl_currentfood WHERE useruid = %d AND deleted = 0 AND isconsumed = 0", useruid];
    FMResultSet *results = [_fmdb executeQuery:selectSql];
    if (results)
    {
        NSMutableArray *foods = [[NSMutableArray alloc] init];
        while ([results next]) {
            CurrentFood *food = [[CurrentFood alloc] init];
            food.uid = [results intForColumn:@"fooduid"];
            food.brand = [results stringForColumn:@"brand"];
            food.name = [results stringForColumn:@"name"];
            food.servingsize = [results stringForColumn:@"servingsize"];
            food.calories = [results doubleForColumn:@"calories"];
            food.protein = [results stringForColumn:@"protein"];
            food.carbs = [results stringForColumn:@"carbs"];
            food.fat = [results stringForColumn:@"fat"];
            food.image = [results stringForColumn:@"image"];
            food.useruid = [results intForColumn:@"useruid"];
            food.isCustom = [results intForColumn:@"iscustom"];
            
            food.currentUid = [results intForColumn:@"uid"];
            food.isConsumed = [results intForColumn:@"isconsumed"];
            food.consumedDate = [CommonMethods str2date:[results stringForColumn:@"consumeddate"] withFormat:DATETIME_FORMAT];
            [foods addObject:food];
        }
        success(foods);
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", selectSql]);
    }
}

- (void)getCurrentFoods:(int)useruid withDate:(NSDate *)date success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    NSString *strDate = [CommonMethods date2str:date withFormat:DATE_FORMAT];
    
    NSString *selectSql = [NSString stringWithFormat:@"SELECT * FROM tbl_currentfood WHERE useruid = %d AND deleted = 0 AND SUBSTR(createdtime, 1, 10) = '%@'", useruid, strDate];
    FMResultSet *results = [_fmdb executeQuery:selectSql];
    if (results)
    {
        NSMutableArray *foods = [[NSMutableArray alloc] init];
        while ([results next]) {
            CurrentFood *food = [[CurrentFood alloc] init];
            food.uid = [results intForColumn:@"fooduid"];
            food.brand = [results stringForColumn:@"brand"];
            food.name = [results stringForColumn:@"name"];
            food.servingsize = [results stringForColumn:@"servingsize"];
            food.calories = [results doubleForColumn:@"calories"];
            food.protein = [results stringForColumn:@"protein"];
            food.carbs = [results stringForColumn:@"carbs"];
            food.fat = [results stringForColumn:@"fat"];
            food.image = [results stringForColumn:@"image"];
            food.useruid = [results intForColumn:@"useruid"];
            food.isCustom = [results intForColumn:@"iscustom"];
            
            food.currentUid = [results intForColumn:@"uid"];
            food.isConsumed = [results intForColumn:@"isconsumed"];
            food.consumedDate = [CommonMethods str2date:[results stringForColumn:@"consumedtime"] withFormat:DATETIME_FORMAT];
            [foods addObject:food];
        }
        success(foods);
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", selectSql]);
    }
}

- (void)consumedFoods:(int)useruid foods:(NSMutableArray *)foods
{
    NSString *strDate = [CommonMethods date2str:[NSDate date] withFormat:DATETIME_FORMAT];
    for (Food *food in foods) {
        NSString *updateSql = [NSString stringWithFormat:@"UPDATE tbl_currentfood SET consumeddate = '%@', isconsumed = 1 WHERE fooduid = %d AND useruid = %d iscustom = %d AND deleted = 0", strDate, food.uid, useruid, food.isCustom];
        BOOL bRet = [_fmdb executeUpdate:updateSql];
        if (bRet)
        {
            //success();
        }
        else
        {
            //failure([NSString stringWithFormat:@"SQL error: %@", updateSql]);
        }
    }
}


#pragma mark - Favorites foods

- (void)addFoodToFavorites:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *selectSql = [NSString stringWithFormat:@"SELECT count(*) as recordcount FROM tbl_favoritefood WHERE deleted = 0 and useruid = %d AND iscustom = %d AND fooduid = %d", useruid, food.isCustom, food.uid];
    FMResultSet *results = [_fmdb executeQuery:selectSql];
    if (results)
    {
        if ([results next])
        {
            int count = [results intForColumn:@"recordcount"];
            if (count > 0)
            {
                failure(@"Already exist in favorites food");
                return;
            }
        }
    }
    
    NSString *strDate = [CommonMethods date2str:[NSDate date] withFormat:DATETIME_FORMAT];
    NSString *insertSql = [NSString stringWithFormat:@"INSERT INTO tbl_favoritefood(fooduid,  brand, name, servingsize, calories, protein, carbs, fat, image, createdtime, useruid, iscustom, deleted) VALUES(%d, '%@', '%@', '%@', %f, '%@', '%@', '%@', '%@', '%@', %d, %d, 0)",
                           food.uid,
                           food.brand,
                           food.name,
                           food.servingsize,
                           food.calories,
                           food.protein,
                           food.carbs,
                           food.fat,
                           food.image,
                           strDate,
                           useruid,
                           food.isCustom];
    BOOL bRet = [_fmdb executeUpdate:insertSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", insertSql]);
    }
}

- (void)removeFoodFromFavorites:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *updateSql = [NSString stringWithFormat:@"UPDATE tbl_favoritefood SET deleted = 1 WHERE fooduid = %d AND useruid = %d AND iscustom = %d AND deleted = 0", food.uid, useruid, food.isCustom];
    BOOL bRet = [_fmdb executeUpdate:updateSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", updateSql]);
    }
}


- (void)getFavoritesFoods:(int)useruid success:(void (^)(NSMutableArray *foods))success failure:(void (^)(NSString *))failure
{
    NSString *selectSql = [NSString stringWithFormat:@"SELECT * FROM tbl_favoritefood WHERE useruid = %d AND deleted = 0", useruid];
    FMResultSet *results = [_fmdb executeQuery:selectSql];
    if (results)
    {
        NSMutableArray *foods = [[NSMutableArray alloc] init];
        while ([results next]) {
            Food *food = [[Food alloc] init];
            food.uid = [results intForColumn:@"fooduid"];
            food.brand = [results stringForColumn:@"brand"];
            food.name = [results stringForColumn:@"name"];
            food.servingsize = [results stringForColumn:@"servingsize"];
            food.calories = [results doubleForColumn:@"calories"];
            food.protein = [results stringForColumn:@"protein"];
            food.carbs = [results stringForColumn:@"carbs"];
            food.fat = [results stringForColumn:@"fat"];
            food.image = [results stringForColumn:@"image"];
            food.useruid = [results intForColumn:@"useruid"];
            food.isCustom = [results intForColumn:@"iscustom"];
            [foods addObject:food];
        }
        success(foods);
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", selectSql]);
    }
}


#pragma  mark - Consumed

- (void)addConsumed:(int)useruid withConsumed:(Consumed *)consumed success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSString *insertSql = [NSString stringWithFormat:@"INSERT INTO tbl_consumed(useruid, consumeddate, createdtime, stepstaken, caloriesconsumed, mileswalked, deleted) VALUES(%d, '%@', '%@', %d, %f, %f, 0)", useruid, [CommonMethods date2str:consumed.date withFormat:DATETIME_FORMAT], [CommonMethods date2str:[NSDate date] withFormat:DATETIME_FORMAT], consumed.stepsTaken, consumed.caloriesConsumed, consumed.milesWalked];
    BOOL bRet = [_fmdb executeUpdate:insertSql];
    if (bRet)
    {
        success();
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", insertSql]);
    }
}

- (void)getConsumed:(int)useruid withDate:(NSDate *)date success:(void (^)(Consumed *))success failure:(void (^)(NSString *))failure
{
    NSString *strDate = [CommonMethods date2str:date withFormat:DATE_FORMAT];
    NSString *selectSql = [NSString stringWithFormat:@"SELECT * FROM tbl_consumed WHERE useruid = %d AND deleted = 0 AND SUBSTR(consumeddate, 1, 10) = '%@' ", useruid, strDate];
    FMResultSet *results = [_fmdb executeQuery:selectSql];
    if (results)
    {
        if ([results next]) {
            Consumed *consumed = [[Consumed alloc] init];
            consumed.date = [CommonMethods str2date:[results stringForColumn:@"consumeddate"] withFormat:DATETIME_FORMAT];
            consumed.stepsTaken = [results intForColumn:@"stepstaken"];
            consumed.caloriesConsumed = [results doubleForColumn:@"caloreisconsumed"];
            consumed.milesWalked = [results doubleForColumn:@"mileswalked"];
            success(consumed);
        }
        else
            failure([NSString stringWithFormat:@"No data : %@", selectSql]);
    }
    else
    {
        failure([NSString stringWithFormat:@"SQL error: %@", selectSql]);
    }
}



@end
