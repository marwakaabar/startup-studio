from flask import Flask, request, jsonify
from detoxify import Detoxify
import os
import numpy as np

app = Flask(__name__)

MODEL_TYPE = os.environ.get('DETOXIFY_MODEL', 'multilingual')
models = {
    'multilingual': Detoxify('multilingual'),
    'original': Detoxify('original'),
    'unbiased': Detoxify('unbiased')
}

detoxify_model = models.get(MODEL_TYPE, models['multilingual'])

def convert_numpy_types(obj):
    """Convert numpy types to native Python types for JSON serialization"""
    if isinstance(obj, np.ndarray):
        return obj.tolist()
    elif isinstance(obj, (np.float32, np.float64)):
        return float(obj)
    elif isinstance(obj, (np.int32, np.int64)):
        return int(obj)
    elif isinstance(obj, dict):
        return {key: convert_numpy_types(value) for key, value in obj.items()}
    elif isinstance(obj, list):
        return [convert_numpy_types(item) for item in obj]
    return obj

@app.route("/moderate", methods=["POST"])
def moderate():
    data = request.get_json()
    text = data.get("text", "")
    lang = data.get("lang", "fr")  
    
    results = detoxify_model.predict(text)
    
    results = convert_numpy_types(results)
    
    threshold = float(os.environ.get('TOXICITY_THRESHOLD', '0.7'))
    
    is_toxic = any(score > threshold for score in results.values())
    
    most_toxic_category = max(results.items(), key=lambda x: x[1]) if is_toxic else None
    
    return jsonify({
        "is_toxic": is_toxic,
        "scores": results,
        "most_toxic_category": most_toxic_category[0] if most_toxic_category else None,
        "most_toxic_score": most_toxic_category[1] if most_toxic_category else None,
        "lang": lang
    })

@app.route("/health", methods=["GET"])
def health_check():
    return jsonify({"status": "ok", "model": MODEL_TYPE})

if __name__ == "__main__":
    port = int(os.environ.get('MODERATOR_PORT', 5001))
    debug_mode = os.environ.get('MODERATOR_DEBUG', 'False').lower() == 'true'
    app.run(host='0.0.0.0', port=port, debug=debug_mode)
