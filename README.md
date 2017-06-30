# Lightweight Web-Based Picture Resizer Service

## What does it do?
This PHP Picture Reszier is a web-based picture resizer service that uses AWS to perform all of the heavy lifting. This service is unique because it allows the user to upload their files directly to a private AWS S3 bucket with all of the proper authentication, and then download those files from a separate private bucket after the resizing is complete, and it does all of this without the user uploading any files to your own server. This makes the service quite easy to scale, as your server is only really responsible for generating the authentication for each file.

Note on AWS setup: This software assumes that you have set up four basic components on AWS (I will explain how to do this later):
1.  An input S3 bucket
1.  An output S3 bucket
1.  A Lambda function that takes a file from the input bucket, and then puts a resulting file in the output bucket.
1.  An IAM user with full S3 access permissions.

## How can I use this myself?
There are three steps in setting up this service for your own personal use:
1.  Make an AWS account, and set up an input S3 bucket, a lambda function to process the inputs, an output S3 bucket, and an IAM user with permission to access S3. (Explained further below)
2.  Setup a PHP server to run your own code off of.
3.  Download this repository and place it on your server, then update the config file according to your own AWS configuration and credentials.

### Setting Up AWS
(This is walkthrough is heavily influenced by the official AWS tutorial found [here](http://docs.aws.amazon.com/lambda/latest/dg/with-s3-example.html), but I think you will find this guide to be faster and easier to follow.)

If you don't already have an AWS account, go ahead and do that first. The free tier should be enough for most people. Then follow the instructions [here](http://docs.aws.amazon.com/cli/latest/userguide/installing.html) to install the AWS Command Line Interface. Also install this repository if you haven't already done that. You'll need it later.

#### Creating an IAM User
Once you have your AWS account, go ahead and begin by opening up the **[AWS Management Console](https://aws.amazon.com/console/)** and logging in to your account. Click on the **Services** dropdown in the upper-left corner and then click on **IAM** under the Security, Identity & (???) heading. Next, click **Users** on the navbar to the left. At the top, click **Add user** to begin creating a new user. Give the user a descriptive name (like 'Bucket-Master' or something interesting like that), then click **Next: Permissions**. The user will only need one policy, so let's just attach it directly. Click **Attach existing policies directly** then search for **AmazonS3FullAccess**, check the box next to it. Click **Next: Review**. Look over the review to make sure everything looks correct, then click **Create user**. On the next page you should see the Access key ID and Secret access key for the new user. Record both of these somewhere safe away from prying eyes where you can get to them later. You can delete and regenerate these keys later if you need to, but it would probably be better to just put them somewhere safe.

#### Creating the S3 Buckets
Click on the **Services** dropdown in the upper-left corner and then click on **S3** under the Storage heading. Click **Create bucket**. Give your bucket a descriptive name (like 'image-input-bucket'), then click **Create**. Note: The bucket name must be unique; no two buckets are allowed to have the same name. Repeat this step again with a different name for your output bucket.

#### Creating the Lambda Function
To create the lambda function, we will start with writing the code the function will execute. AWS Lambda supports several languages for it's functions, but I have found Node.js to be the most painless.

##### Building the Function Code in Node.js
Start by installing [Node.js]() to your machine. After you have finished installing Node.js, you will need to install the following three dependencies:
* AWS SDK for JavaScript in Node.js (AWS already has this installed on their end, you won't need it when you upload to Lambda later)
* gm, GraphicsMagick for node.js
* Async utility module

This has actually already been done for you, but if you want to do it yourself, enter terminal on your machine, navigate to the lambda_function folder from within your copy of PHP Picture Resizer that you downloaded earlier, then type the following command:
```
npm install aws-sdk gm async
```
Now open the image_resizer.js file within the lambda_function folder in the PHP Picture Resizer repository. find the "dstBucket" variable initialization and change "OUTPUT_BUCKET" to whatever you named your output bucket. Finally, zip up the image_resizer.js file and the node_modules folder into a single .zip file (ex. image_resizer.zip) using the following command:
```
zip -r image_resizer.zip /path/to/node_modules /path/to/image_resizer.js
```

##### Creating the Lambda Execution Role in IAM
Click on the **Services** dropdown in the upper-left corner and then click on **IAM** under the Security, Identity & (???) heading. Now click on **Roles** on the navbar to the left. Click **Create new role**. Find **AWS Lambda** from the list under AWS Service Role, and click the **Select** button next to it. Find the policy **AWSLambdaExecute**, check the box next to it, then click **Next Step**. Give the role a detailed name (such as 'lambda_execute_role'), then click **Create role**.

##### Creating the Lambda Function on AWS
Click on the **Services** dropdown in the upper-left corner and then click on **Lambda** under the Compute heading. Click **Create a Lambda function**. Click **Blank Function** as your blueprint. Click in the dashed-outline square, then select **S3** from the dropdown that appeared. For Bucket, click the dropdown and select your input bucket from the list. For Event type, go ahead and select **Object Created (All)**. Click **Next**. For the **Name** field, give your function a descriptive name (such as 'image_resizer'). For the **Runtime** field, select **Node.js 6.10**. For the **Code entry type** field, select **Upload a .ZIP file**, then upload the .zip file you created earlier. For the **Handler** field, input **\<name_of_js_file>.handler** (such as image_resizer.handler). For the **Role** field, select **Choose an existing role**. For the **Existing role** field, select the lambda execution IAM role you built ealier. Now click **Next**. Review the function to make sure everything seems correct, then click **Create function**.

#### Add the Event Source
All that's left now is to give the lambda function the proper permission to execute on an S3 event, and to tell our input bucket to notify the lambda function whenever an object is placed in it.

##### Add Execution Permission to Lambda Function
Run the following command, substituting in your own information into all the angle-bracket spaces:
```
aws lambda add-permission \
--function-name <my_function_name> \
--region <my_region (probably: us-east-1)> \
--statement-id <some-random-unique-id> \
--action "lambda:InvokeFunction" \
--principal s3.amazonaws.com \
--source-arn arn:aws:s3:::<my_input_bucket_name> \
--source-account <bucket-owner-account-id (top right corner in AWS Management Console)> \
--profile <Bucket-Master>
```

##### Add Lambda Notification to S3 Bucket
Click on the **Services** dropdown in the upper-left corner and then click on **S3** under the Storage heading. Click on your input bucket. Click the **Properties** tab. Click the **Events** box underneath Advanced Settings. Click **Add notification**. Give the event a name (such as lambda_notifier). Check the box next to **ObjectCreate (All)**. Select **Lambda Function** from the **Sent to** dropdown. Select your lambda funtion from the **Lambda** dropdown. Click **Save**.

### Setting Up a PHP Server
Lots of variety here. Probably look around for your PHP server implementation of choice and follow their instructions. For the purpose of this guide, the important things is just that you do this. For setting up a localhost server, I found [this guide](https://lukearmstrong.github.io/2016/12/setup-apache-mysql-php-homebrew-macos-sierra/) to be very helpful (I used the php70 instructions).

### Installing and Configuring the PHP Picture Resizer
You're almost done!
Open up the config.php file within the config folder and change all of the information within to match all of your own AWS information. If you need a new AWS Access Key ID and AWS Secret Access Key you can always do it from within IAM in the AWS Management Console. Just open up the **Users** page from the navbar on the left, click on your the equivalent of your 'Bucket-Master' user, navigate to the **Security credentials** tab, then use **Create access key** to your heart's content!

Now just transfer your PHP Picture Resizer repository onto your PHP server, and you should be good to go! Try it out!
