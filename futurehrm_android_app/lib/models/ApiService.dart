import 'dart:convert';

import 'package:futurehrm_android_app/models/response_data.dart';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = "http://26.144.42.43:8888";
  static const String imgUrl = "http://192.168.1.18:8001";

  static Future<ResponseData> get(String endpoint,
      {Map<String, String>? headers}) async {
    final url = Uri.parse('$baseUrl/$endpoint');
    final response = await http.get(url, headers: headers);

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
