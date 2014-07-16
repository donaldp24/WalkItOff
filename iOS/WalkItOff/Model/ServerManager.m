//
//  SeverManager.m
//  WalkItOff
//
//  Created by Donald Pae on 7/2/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "ServerManager.h"
#import "Reachability.h"
#import <sys/socket.h>
#import <netinet/in.h>
#import "AFNetworking.h"
#import "SBJson.h"

static ServerManager *_sharedServerManager = nil;

NSString * const WalkItOffErrorDomain = @"com.walkitoff";

#define kDescriptionNotReachable    @"Network error"
#define ErrorFromNotReachable   ([NSError errorWithDomain:WalkItOffErrorDomain code:ServiceErrorNetwork userInfo:@{NSLocalizedDescriptionKey:kDescriptionNotReachable}])

@implementation ServerManager

+ (ServerManager *)sharedManager
{
    if (_sharedServerManager == nil)
        _sharedServerManager = [[ServerManager alloc] init];
    return _sharedServerManager;
}

- (BOOL)hasConnectivity
{
    // test reachability
    struct sockaddr_in zeroAddress;
    bzero(&zeroAddress, sizeof(zeroAddress));
    zeroAddress.sin_len = sizeof(zeroAddress);
    zeroAddress.sin_family = AF_INET;
    
    Reachability *reachability = [Reachability reachabilityWithAddress:&zeroAddress];
    if (reachability != nil)
    {
        if ([reachability isReachable])
            return YES;
        return NO;
    }
    return NO;
}

- (void)callMethodName:(NSString *)methodName isGet:(BOOL)isGet params:(NSDictionary *)params completion:(void (^)(NSDictionary *, NSError *))handler
{
    if (![self hasConnectivity])
    {
        NSLog(@"Request error, network error");
        handler(nil, ErrorFromNotReachable);
        return;
    }
    
    NSURL  *url = [NSURL URLWithString:SERVICE_URL];
	AFHTTPClient  *httpClient = [[AFHTTPClient alloc] initWithBaseURL:url];
    
    void (^successHandler)(AFHTTPRequestOperation *operation, id responseObject)  = ^(AFHTTPRequestOperation *operation, id responseObject)
    {
        if (responseObject == nil)
        {
            NSLog(@"Request error, responseObject = nil");
            handler(nil, [NSError errorWithDomain:WalkItOffErrorDomain code:ServiceErrorNoResponse userInfo:nil]);
        }
        else
        {
            NSString *responseStr = [[NSString alloc] initWithData:responseObject encoding:NSUTF8StringEncoding];
            NSDictionary *responseDic = [responseStr JSONValue];
            NSLog(@"Request Successful, response '%@'", responseStr);
            handler(responseDic, nil);
        }
        
    };
    
    void (^errorHandler)(AFHTTPRequestOperation *operation, NSError *error)  = ^ (AFHTTPRequestOperation *operation, NSError *error)
    {
        NSLog(@"[HTTPClient Error]: %@", error.localizedDescription);
        handler(nil, error);
    };
    
    if (isGet)
    {
        [httpClient getPath:methodName parameters:params success:successHandler failure:errorHandler];
    }
    else
    {
        [httpClient postPath:methodName parameters:params success:successHandler failure:errorHandler];
    }
    
}

- (void)getMethod:(NSString *)methodName params:(NSDictionary *)params handler:(ServerManagerRequestHandlerBlock)handler
{
    [self callMethodName:methodName isGet:YES params:params completion:handler];
}

- (void)postMethod:(NSString *)methodName params:(NSDictionary *)params handler:(ServerManagerRequestHandlerBlock)handler
{
    [self callMethodName:methodName isGet:NO params:params completion:handler];
}
@end
