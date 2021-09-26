# WooCommPlugin
wordpress plugin to be used with woocommerce for gst calculation

1. setup wordpress in the system [can use https://youtu.be/RtXA60HewUw]
2. install woocommerce and fill the store details like store location
3. clone this repo at **./wp-content/plugins/**
-3.1 You can either download this repo and extract the folder at the given path
-3.2 You can use git clone after forking the repo
4. activate the plugin from the dashboard
5. in the tax section (shows only on enabling taxes) of woocommerce settings specify annd save the hsn code for shop
6. inside the "Standard rates" tax class (or whichever tax class you are using) import the csv  ***tax_rates_to_upload.csv*** from the WooCommPlugin/public directory.
7.


Troubleshoot TnC:
If you are unable to view the Terms and Conditions post. Go to permalinks in settings of wordpress website. Change the **Custom structure** to */%category%/%postname%*

