import 'dart:convert';

import 'package:futurehrm_android_app/models/response_data.dart';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = "http://26.144.42.43:8888";
  static const String imgUrl = "http://26.144.42.43:8001";

  static Future<ResponseData> get(String endpoint,
      {Map<String, String>? headers,
      Map<String, dynamic>? queryParams,
      Map<String, dynamic>? body}) async {
    print("GET: $endpoint");
    print("Query Parameters: ${json.encode(queryParams)}");
    print("Headers: ${json.encode(headers)}");
    print("Body: ${json.encode(body)}");

    // Create the base URL
    final url = Uri.parse('$baseUrl/$endpoint');

    // Add query parameters to the URL
    final uri = url.replace(
        queryParameters:
            queryParams?.map((key, value) => MapEntry(key, value.toString())));

    // Create the GET request
    final request = http.Request('GET', uri);

    // Add headers to the request if they exist
    if (headers != null) {
      request.headers.addAll(headers);
    }

    // Add body to the request if it exists (not standard for GET requests)
    if (body != null) {
      request.body = json.encode(body);
      request.headers['Content-Type'] = 'application/json';
    }

    // Send the request and get the streamed response
    final streamedResponse = await request.send();
    // Convert the streamed response to a regular HTTP response
    final response = await http.Response.fromStream(streamedResponse);

    // Check the status code and process the response accordingly
    if (response.statusCode == 200) {
      return ResponseData.fromMap(json.decode(response.body));
    } else {
      throw Exception('Failed to load data');
    }
  }

  static Future<ResponseData> post(String endpoint, dynamic body,
      {Map<String, String>? headers}) async {
    final url = Uri.parse('$baseUrl/$endpoint');
    headers ??= {};
    headers["Content-Type"] = "application/json";
    final response =
        await http.post(url, headers: headers, body: jsonEncode(body));

    if (response.statusCode == 201 || response.statusCode == 200) {
      return ResponseData.fromMap(jsonDecode(response.body));
    } else {
      throw Exception('Failed to post data');
    }
  }

  static Future<ResponseData> put(String endpoint, dynamic body,
      {Map<String, String>? headers}) async {
    final url = Uri.parse('$baseUrl/$endpoint');
    headers ??= {};
    headers["Content-Type"] = "application/json";
    final response =
        await http.put(url, headers: headers, body: jsonEncode(body));

    if (response.statusCode == 200) {
      return ResponseData.fromMap(jsonDecode(response.body));
    } else {
      throw Exception('Failed to update data');
    }
  }

  static Future<ResponseData> delete(String endpoint,
      {Map<String, String>? headers}) async {
    final url = Uri.parse('$baseUrl/$endpoint');
    final response = await http.delete(url, headers: headers);

    if (response.statusCode == 200) {
      return ResponseData.fromMap(jsonDecode(response.body));
    } else {
      throw Exception('Failed to delete data');
    }
  }
}
