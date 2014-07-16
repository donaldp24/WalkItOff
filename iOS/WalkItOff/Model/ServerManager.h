//
//  SeverManager.h
//  WalkItOff
//
//  Created by Donald Pae on 7/2/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "ServiceErrorCodes.h"

#define SERVICE_URL @"http://192.168.1.22:8201/service/api_json/"

// common response keys
#define kResponseErrorKey   @"error"
#define kResponseDataKey     @"data"
#define kResponseMsgKey     @"msg"

#define DEF_SERVERMANAGER   ServerManager *manager = [ServerManager sharedManager];

typedef void (^ServerManagerRequestHandlerBlock)(NSDictionary *, NSError *);

@interface ServerManager : NSObject

+ (ServerManager *)sharedManager;

- (void)getMethod:(NSString *)methodName params:(NSDictionary *)params handler:(ServerManagerRequestHandlerBlock)handler;
- (void)postMethod:(NSString *)methodName params:(NSDictionary *)params handler:(ServerManagerRequestHandlerBlock)handler;

@end
