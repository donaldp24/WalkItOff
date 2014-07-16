//
//  Operation.h
//  WalkItOff
//
//  Created by Donald Pae on 7/11/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#ifndef WalkItOff_Operation_h
#define WalkItOff_Operation_h

typedef enum {
    OperationTypeAddConsumed = 0
} OperationType;

@interface Operation : NSObject

@property (nonatomic) int uid;
@property (nonatomic) OperationType type;
@property (nonatomic, strong) NSMutableDictionary *params;
@property (nonatomic, strong) NSDate *timestamp;

@end

#endif
