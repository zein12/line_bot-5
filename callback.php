<?php

class LineApplication {

    protected $keyword = [
        // HandleCommand
        'betago' => 'BetaGoCommand',
        // GoogleCommand
        'google' => 'GoogleCommand'
    ];
    
    public function callback() {
    
        $input = json_decode(file_get_contents('php://input'), TRUE);
        
        if(empty($input) && $input['content']['contentType'] != '1') {
            
            $output = $this->handle($input);
            
        } else {
            $command = explode(' ', $input['content']['text']);
            
            if(isset($this->keyword[$command[0]])) {
                
                $handle = new $this->keyword[$command[0]];
                
            } else {
                
                $handle = new HelpCommand;
                
            }
            
            $output = $handle->handle($command);
        }
        
        $this->output($output);
    }
    
    function getContentType($value) {
        $type_code = array(
            1 => "Text message",
            2 => "Image message",
            3 => "Video message",
            4 => "Video message",
            7 => "Location message",
            8 => "Sticker message",
            10 => "Contact message"
        );
        
        if(array_key_exists($value, $type_code)) {
            return $type_code[$value];
        }
        
        return "unknown";
    }

    public function output($message) {
        echo json_encode(array('msg'=>$message));
    }
    public function handle($command) {
        return 'no service';
    }
    
}

class BetaGoCommand extends LineApplication{
    public function handle($command){
    
        $point_map = [
            ["┌","─","┬","─","┬","─","┬","─","┬","─","┐"],
            ["├","─","┼","─","┼","─","┼","─","┼","─","┤"],
            ["├","─","┼","─","┼","─","┼","─","┼","─","┤"],
            ["├","─","┼","─","┼","─","┼","─","┼","─","┤"],
            ["├","─","┼","─","┼","─","┼","─","┼","─","┤"],
            ["└","─","┴","─","┴","─","┴","─","┴","─","┘"]
        ];

        $x = $command[1];
        $x = $x < 1 ? 1 : $x;
        $x = $x > 4 ? 4 : $x;
        
        $y = $command[2];
        $y = $y < 1 ? 1 : $y;
        $y = $y > 4 ? 4 : $y;
        $y = $y * 2;
        
        $pc_x = array_diff([1, 2, 3, 4], [$x]);
        sort($pc_x);
        $pc_y = array_diff([2, 4, 6, 8], [$y]);
        sort($pc_y);
        
        $pc_x = $pc_x[rand(0, count($pc_x)-1)];
        $pc_y = $pc_y[rand(0, count($pc_y)-1)];
        
        $point_map[$x][$y] = "●";
        $point_map[$pc_x][$pc_y] = "○";
        
        $str = "BetaGo v0.0.0.1\r\n";
        $str .= "your is black, coordinate is (" . $command[1] . ", " . $command[2] . ").\r\n";
        $str .= "pc is white, coordinate is (" . $pc_x . ", ". ($pc_y/2) . ").\r\n";

        foreach($point_map as $row) {
            foreach($row as $col) {
                $str .= $col;
            }
            $str .= "\r\n";
        }
        
        return $str;
    }
}
class GoogleCommand extends LineApplication {
    public function handle($command) {
        return 'https://www.google.com.tw/?#q='.urlencode($command[1]);
    }
}

class HelpCommand extends LineApplication {
    public function handle($command) {
        $message = [
            'Sesuatu yang salah?',
            'Sakit itu?',
            'mengapa menarik',
            'Oh konyol',
            '....',
            'Ah Anda untuk mess !!',
            'www',
            '( ’?`)y??~~'
        ];
        return $message[rand(0, 7)];
    }
}

$bot = new LineApplication;
$bot->callback();
