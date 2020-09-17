 import	javax.crypto.Mac;
import	javax.crypto.spec.SecretKeySpec; 
 import	javax.xml.bind.DatatypeConverter; 
 import	java.net.URLEncoder;
import	java.security.InvalidKeyException; 
 import	java.security.NoSuchAlgorithmException;
import	java.util.*;

class signurl{
	public String sign_generate(String str, String userSecretKey) throws NoSuchAlgorithmException, InvalidKeyException {

    Mac localMac = Mac.getInstance("HmacSHA1");
    SecretKeySpec localSecretKeySec = new SecretKeySpec(userSecretKey.getBytes(), "HmacSHA1");

	localMac.init(localSecretKeySec);
    byte[] arrayOfByte = localMac.doFinal(str.toLowerCase().getBytes());

   return DatatypeConverter.printBase64Binary(arrayOfByte);
  }

	public static void main(String args[]) throws Exception {
		signurl a;
		a = new signurl();
		String sign = a.sign_generate(args[0],args[1]);
		String value = URLEncoder.encode(sign,"UTF-8");
		System.out.println(value);

	}
}
