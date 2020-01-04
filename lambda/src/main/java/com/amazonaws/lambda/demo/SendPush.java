package com.amazonaws.lambda.demo;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import javax.net.ssl.HttpsURLConnection;

import org.apache.commons.codec.digest.DigestUtils;

import com.amazonaws.AmazonClientException;
import com.amazonaws.services.lambda.runtime.Context;
import com.amazonaws.services.lambda.runtime.LambdaLogger;
import com.amazonaws.services.lambda.runtime.RequestHandler;
import com.amazonaws.services.lambda.runtime.events.SQSEvent;
import com.amazonaws.services.lambda.runtime.events.SQSEvent.MessageAttribute;
import com.amazonaws.services.lambda.runtime.events.SQSEvent.SQSMessage;
import com.amazonaws.services.sns.AmazonSNS;
import com.amazonaws.services.sns.AmazonSNSClientBuilder;
import com.amazonaws.services.sns.model.EndpointDisabledException;
import com.amazonaws.services.sns.model.PublishRequest;
import com.amazonaws.services.sns.model.PublishResult;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;

public class SendPush implements RequestHandler<SQSEvent, Void>{
    @Override
    public Void handleRequest(SQSEvent event, Context context) {
        LambdaLogger logger = context.getLogger();

        if (event.equals(null)) {
        	logger.log("SQS event is null");
        	return null;
        }

        logger.log("Event: " + event.toString());

        AmazonSNS snsClient = AmazonSNSClientBuilder.standard().withRegion("eu-west-1").build();

        try {
	        for (SQSMessage msg : event.getRecords()) {
	            String body = msg.getBody();
	            if (body.isEmpty()) continue;

	            logger.log("Read attributes");
	            Map<String, MessageAttribute> msgAttributes = msg.getMessageAttributes();
	            logger.log(msgAttributes.toString());

	            if (!msgAttributes.containsKey("tokens")) continue;
	            String[] tokens = msgAttributes.get("tokens").getStringValue().split(";");
	            if (tokens.length == 0) continue;

	            String contentType = msgAttributes.get("contentType").getStringValue();
	            String contentId = msgAttributes.get("contentId").getStringValue();
	            String title = msgAttributes.get("title").getStringValue();
	            String badge = msgAttributes.containsKey("badge") ? msgAttributes.get("badge").getStringValue() : "";
              String callbackUrl = msgAttributes.containsKey("callback_url") ? msgAttributes.get("callback_url").getStringValue() : "";

	            logger.log("Build payload");
	            HashMap<String, String> notificationMap = new HashMap<>();
	            notificationMap.put("title", title);
	            notificationMap.put("text", body);
	            notificationMap.put("contentType", contentType);
	            notificationMap.put("contentId", contentId);
	            if (!badge.isEmpty()) notificationMap.put("badge", badge);
	            notificationMap.put("priority", "2");
	            notificationMap.put("importance", "5");
	            notificationMap.put("sound", "0");

            	logger.log("Build push payload");
	            HashMap<String, HashMap<String, String>> pushPayloadMap = new HashMap<>();
				      pushPayloadMap.put("notification", notificationMap);
				      pushPayloadMap.put("data", notificationMap);
	            logger.log(pushPayloadMap.toString());

	            logger.log("Build JSON");
				      ObjectMapper mapper = new ObjectMapper();
	            HashMap<String, String> snsMessageMap = new HashMap<>();
	            try {
						     snsMessageMap.put("GCM", mapper.writeValueAsString(pushPayloadMap));
				      } catch (JsonProcessingException e) {
			            logger.log(e.getMessage());
                  continue;
				      }

              logger.log("Build push message");
				      String snsMessage = mapper.writeValueAsString(snsMessageMap);

	            ArrayList<String> disabledTokens = new ArrayList<>();

	            try {
		            for (String token: tokens) {
		            	logger.log("Sending push to " + token.toString());

		            	try {
			            	PublishResult result = snsClient.publish(new PublishRequest()
		            			.withMessage(snsMessage)
		                        .withTargetArn(token)
		                        .withMessageStructure("json"));

			            	logger.log(result.toString());
		            	} catch (EndpointDisabledException e) {
		            		disabledTokens.add(token);
		            	}
		            }
				      } catch (JsonProcessingException e) {
					       logger.log(e.getMessage());
				      }

              logger.log("Send callback notification");
              if (!callbackUrl.isEmpty()) {
    				      try {
    	            	logger.log("Notify url: " + callbackUrl);
    	            	String postData = "push_id=" + contentId);
    
    	            	if (!disabledTokens.isEmpty()) {
    	            		postData += "&disabled_tokens=" + String.join(",", disabledTokens);
    	            	}

    	            	URL notifiedUrl = new URL(callbackUrl);
    	              HttpsURLConnection con = (HttpsURLConnection) notifiedUrl.openConnection();
    	              con.setRequestMethod("POST");
    	              con.setDoOutput(true);
    	              con.getOutputStream().write(postData.getBytes("UTF-8"));
    	              con.getInputStream();

    	            	logger.log("Completed notify");
    				      }
    				      catch (MalformedURLException e) {
    					       logger.log(e.toString());
    				      }
    				      catch (IOException e) {
    					       logger.log(e.toString());
    				      }
              }
	        }
        }
        catch (NullPointerException e) {
        	logger.log(e.toString());
        }
        catch (AmazonClientException e) {
        	logger.log(e.toString());
        }

        return null;
    }
}