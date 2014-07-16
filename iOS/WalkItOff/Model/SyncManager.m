//
//  SyncManager.m
//  WalkItOff
//
//  Created by Donald Pae on 7/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "SyncManager.h"
#import "Database+walkitoff.h"
#import "ServerManager.h"

static SyncManager *_sharedSyncManager = nil;

@implementation SyncManager

+ (SyncManager *)sharedSyncManager {
    if (_sharedSyncManager == nil)
        _sharedSyncManager = [[SyncManager alloc] init];
    return _sharedSyncManager;
}

- (void)startSync
{
    Database *db = [Database sharedDatabase];
    //
}

@end
