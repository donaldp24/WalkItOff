//
//  SyncManager.h
//  WalkItOff
//
//  Created by Donald Pae on 7/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "Operation.h"


@interface SyncManager : NSObject

+ (SyncManager *)sharedSyncManager;

/**
 * upload all datas saved on local
 */
- (void)startSync;



@end
