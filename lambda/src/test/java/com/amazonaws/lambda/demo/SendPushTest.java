package com.amazonaws.lambda.demo;

import org.junit.Assert;
import org.junit.BeforeClass;
import org.junit.Test;

import com.amazonaws.services.lambda.runtime.Context;
import com.amazonaws.services.lambda.runtime.events.SQSEvent;

/**
 * A simple test harness for locally invoking your Lambda function handler.
 */
public class SendPushTest {

    private static SQSEvent event;

    @BeforeClass
    public static void createEvent() {
        // TODO: set up your sample input object here.
        event = new SQSEvent();
    }

    private Context createContext() {
        TestContext ctx = new TestContext();

        // TODO: customize your context here if needed.
        ctx.setFunctionName("SendPush");

        return ctx;
    }

    @Test
    public void testSendPush() {
        SendPush handler = new SendPush();
        Context ctx = createContext();

        Assert.assertNull(handler.handleRequest(event, ctx));
    }
}
